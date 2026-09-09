<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Mail\StatusUpdateMail;
use App\Models\Customer;
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

    public function history(Request $request)
    {
        $filters = $request->only(['search', 'engineer', 'engineer_id', 'area', 'date_from', 'date_to']);

        $services = Service::query()
            ->with('engineer')
            ->completed()
            ->filter($filters)
            ->latest('visit_date')
            ->paginate(15)
            ->withQueryString();

        return view('service-history', [
            'services' => $services,
            'filters' => $filters,
            'engineers' => $this->engineerOptions(),
        ]);
    }

    public function create()
    {
        return view('services.create', [
            'engineers' => $this->engineerOptions(),
            'customers' => Customer::orderBy('name')->get(['name', 'email', 'region']),
        ]);
    }

    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        // Set default values for fields that have database constraints but are optional in form
        $validated['service_type'] = $validated['service_type'] ?? 'PPM';
        $validated['status'] = $validated['status'] ?? 'scheduled';

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

        // Send email notification for service creation
        $this->notificationService->sendServiceCreated($service, auth()->user());

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
            'customers' => Customer::orderBy('name')->get(['name', 'email', 'region']),
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

        // Send email notification for service update
        $this->notificationService->sendServiceUpdated($service, auth()->user());

        // Send email notification if status changed (using the specific status change method)
        if ($oldStatus !== $newStatus) {
            $this->notificationService->sendStatusUpdate(
                'Service',
                $service->job_ref,
                $oldStatus,
                $newStatus,
                'Status Update',
                auth()->user()
            );
        }

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

    private function engineerOptions()
    {
        return User::engineers()->active()->orderBy('name')->get();
    }
}
