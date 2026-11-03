<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Mail\StatusUpdateMail;
use App\Models\Customer;
use App\Models\Region;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'engineer', 'engineer_id', 'service_type', 'area', 'date_from', 'date_to']);

        $services = Service::query()
            ->with('engineer')
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('services.index', [
            'services' => $services,
            'filters' => $filters,
            'engineers' => $this->engineerOptions(),
            'statuses' => Service::STATUSES,
            'serviceTypes' => Service::SERVICE_TYPES,
        ]);
    }

    public function create(): View
    {
        return view('services.create', [
            'engineers' => $this->engineerOptions(),
            'customers' => Customer::with(['customerSites', 'customerSites.doors'])->orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        // Set default values for fields that have database constraints but are optional in form
        $validated['service_type'] = $validated['service_type'] ?? 'PPM';
        $validated['status'] = $validated['status'] ?? 'scheduled';
        $validated['remedial_required'] = $validated['remedial_required'] ?? 'no';

        // Calculate next email due date if mailing frequency is set but next_email_due is not provided
        if (!empty($validated['mailing_frequency']) && empty($validated['next_email_due'])) {
            $validated['next_email_due'] = $this->calculateNextEmailDate($validated['mailing_frequency']);
        }

        $validated = $this->applyEngineerAssignment($validated);
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $service = Service::create($validated);

        ActivityLogger::log(
            'service.created',
            'services',
            "Created service job {$service->job_ref} for {$service->customer_name}",
            $service,
        );

        // Send email notification for service creation asynchronously
        $currentUser = auth()->user();
        dispatch(function () use ($service, $currentUser) {
            $this->notificationService->sendServiceCreated($service, $currentUser);
        })->afterResponse();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service record created successfully.',
                'redirect' => route('services.index')
            ]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Service record created successfully.');
    }

    public function show(Service $service)
    {
        $service->load('engineer', 'creator', 'updater');

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', [
            'service' => $service,
            'engineers' => $this->engineerOptions(),
            'customers' => Customer::with(['customerSites', 'customerSites.doors'])->orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $validated = $request->validated();

        // Set default values for fields that have database constraints but are optional in form
        if (!isset($validated['service_type']) || $validated['service_type'] === '') {
            $validated['service_type'] = $service->service_type ?? 'PPM';
        }
        if (!isset($validated['status']) || $validated['status'] === '') {
            $validated['status'] = $service->status ?? 'scheduled';
        }
        if (!isset($validated['remedial_required']) || $validated['remedial_required'] === '') {
            $validated['remedial_required'] = $service->remedial_required ?? 'no';
        }

        // Calculate next email due date if mailing frequency is set but next_email_due is not provided
        if (!empty($validated['mailing_frequency']) && empty($validated['next_email_due'])) {
            $validated['next_email_due'] = $this->calculateNextEmailDate($validated['mailing_frequency']);
        }

        $oldStatus = $service->status;
        $newStatus = $validated['status'] ?? $service->status;

        $validated = $this->applyEngineerAssignment($validated);
        $validated['updated_by'] = Auth::id();

        $service->update($validated);

        ActivityLogger::log(
            'service.updated',
            'services',
            "Updated service job {$service->job_ref}",
            $service,
        );

        // Send email notification asynchronously
        $currentUser = auth()->user();
        dispatch(function () use ($service, $oldStatus, $newStatus, $currentUser) {
            if ($oldStatus !== $newStatus) {
                $this->notificationService->sendStatusUpdate(
                    'Service',
                    $service->job_ref,
                    $oldStatus,
                    $newStatus,
                    'Status Update',
                    $currentUser
                );
            } else {
                $this->notificationService->sendServiceUpdated($service, $currentUser);
            }
        })->afterResponse();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service record updated successfully.',
                'redirect' => route('services.index')
            ]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Service record updated successfully.');
    }

    public function destroy(Service $service)
    {
        $serviceData = (object) [
            'job_ref' => $service->job_ref,
            'status' => $service->status
        ];

        $service->delete();

        ActivityLogger::log(
            'service.deleted',
            'services',
            "Deleted service job {$serviceData->job_ref}",
        );

        // Send email notification for service deletion
        $this->notificationService->sendServiceDeleted($serviceData, auth()->user());

        return redirect()->route('services.index')
            ->with('success', 'Service record deleted successfully.');
    }

    private function applyEngineerAssignment(array $validated): array
    {
        $user = Auth::user();

        if ($user?->isEngineer()) {
            $validated['engineer_id'] = $user->id;
            $validated['engineer_name'] = $user->name;
        } elseif (! empty($validated['engineer_id'])) {
            $engineer = User::find($validated['engineer_id']);
            $validated['engineer_name'] = $engineer?->name;
        } elseif (! empty($validated['engineer_name'])) {
            // If engineer_name is provided manually, find the engineer by name
            $engineer = User::where('name', $validated['engineer_name'])->first();
            if ($engineer) {
                $validated['engineer_id'] = $engineer->id;
            }
        }

        return $validated;
    }

    private function calculateNextEmailDate(string $frequency): ?\Carbon\Carbon
    {
        return match($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => null,
        };
    }

    private function engineerOptions()
    {
        return User::engineers()->active()->orderBy('name')->get();
    }
}
