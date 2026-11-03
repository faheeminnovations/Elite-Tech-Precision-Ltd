<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Mail\ContractStatusMail;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerSite;
use App\Models\Region;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContractController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'area', 'frequency', 'date_from', 'date_to']);

        $contracts = Contract::query()
            ->filter($filters)
            ->orderBy('next_ppm_due')
            ->paginate(30)
            ->withQueryString()
            ->onEachSide(1);

        $statusTabs = [
            'all' => ['label' => 'All', 'count' => Contract::count()],
            'active' => ['label' => 'Active', 'count' => Contract::where('status', 'active')->count()],
            'expiring' => ['label' => 'Expiring', 'count' => Contract::where('status', 'expiring')->count()],
            'expired' => ['label' => 'Expired', 'count' => Contract::where('status', 'expired')->count()],
            'cancelled' => ['label' => 'Cancelled', 'count' => Contract::where('status', 'cancelled')->count()],
            'overdue' => ['label' => 'Overdue', 'count' => Contract::where('status', 'overdue')->count()],
        ];

        $areas = Contract::query()
            ->whereNotNull('area')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        return view('contracts.index', [
            'contracts' => $contracts,
            'filters' => $filters,
            'statusTabs' => $statusTabs,
            'frequencies' => Contract::FREQUENCIES,
            'areas' => $areas,
        ]);
    }

    public function create(): View
    {
        $customers = Customer::with(['customerSites', 'customerSites.doors'])->orderBy('name')->get();
        
        // Pre-process customer sites with doors for JavaScript
        $customerSitesData = $customers->mapWithKeys(function ($customer) {
            return [
                $customer->id => $customer->customerSites->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'name' => $site->site_name,
                        'region_id' => $site->region_id,
                        'doors' => $site->doors ? $site->doors->map(function ($door) {
                            return [
                                'id' => $door->id,
                                'name' => $door->door_name
                            ];
                        })->toArray() : []
                    ];
                })->toArray()
            ];
        })->toArray();

        return view('contracts.create', [
            'customers' => $customers,
            'customerSitesData' => $customerSitesData,
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    public function store(ContractRequest $request)
    {
        $validated = $request->validated();
        $validated['area'] = $this->resolveArea($validated['customer_name'], $validated['area'] ?? null);

        // Set default values for fields that have database constraints but are optional in form
        $validated['frequency'] = $validated['frequency'] ?? '6 monthly';
        $validated['status'] = $validated['status'] ?? 'upcoming';

        // Calculate next email due date if mailing frequency is set but next_email_due is not provided
        if (!empty($validated['mailing_frequency']) && empty($validated['next_email_due'])) {
            $validated['next_email_due'] = $this->calculateNextEmailDate($validated['mailing_frequency']);
        }

        $contract = Contract::create($validated);

        // Send email notification for contract creation asynchronously
        $currentUser = auth()->user();
        dispatch(function () use ($contract, $currentUser) {
            $this->notificationService->sendContractCreated($contract, $currentUser);
        })->afterResponse();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contract created successfully.',
                'redirect' => $request->input('return_to') === 'data-entry' ? route('data-entry') : route('contracts.index')
            ]);
        }

        return $this->redirectAfterSave($request, 'Contract created successfully.');
    }

    public function show(Contract $contract): View
    {
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $customers = Customer::with(['customerSites', 'customerSites.doors'])->orderBy('name')->get();
        
        // Pre-process customer sites with doors for JavaScript
        $customerSitesData = $customers->mapWithKeys(function ($customer) {
            return [
                $customer->id => $customer->customerSites->map(function ($site) {
                    return [
                        'id' => $site->id,
                        'name' => $site->site_name,
                        'region_id' => $site->region_id,
                        'doors' => $site->doors ? $site->doors->map(function ($door) {
                            return [
                                'id' => $door->id,
                                'name' => $door->door_name
                            ];
                        })->toArray() : []
                    ];
                })->toArray()
            ];
        })->toArray();

        return view('contracts.edit', [
            'contract' => $contract,
            'customers' => $customers,
            'customerSitesData' => $customerSitesData,
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    public function update(ContractRequest $request, Contract $contract)
    {
        $validated = $request->validated();
        $validated['area'] = $this->resolveArea($validated['customer_name'], $validated['area'] ?? null);

        // Set default values for fields that have database constraints but are optional in form
        if (!isset($validated['frequency']) || $validated['frequency'] === '') {
            $validated['frequency'] = $contract->frequency ?? '6 monthly';
        }
        if (!isset($validated['status']) || $validated['status'] === '') {
            $validated['status'] = $contract->status ?? 'upcoming';
        }

        // Calculate next email due date if mailing frequency is set but next_email_due is not provided
        if (!empty($validated['mailing_frequency']) && empty($validated['next_email_due'])) {
            $validated['next_email_due'] = $this->calculateNextEmailDate($validated['mailing_frequency']);
        }

        $oldStatus = $contract->status;
        $newStatus = $validated['status'] ?? $contract->status;

        $contract->update($validated);

        // Send email notification asynchronously
        $currentUser = auth()->user();
        dispatch(function () use ($contract, $oldStatus, $newStatus, $currentUser) {
            if ($oldStatus !== $newStatus) {
                $this->notificationService->sendContractStatusChanged($contract, $oldStatus, $newStatus, $currentUser);
            } else {
                $this->notificationService->sendContractUpdated($contract, $currentUser);
            }
        })->afterResponse();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contract updated successfully.',
                'redirect' => route('contracts.index')
            ]);
        }

        return redirect()->route('contracts.index')->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $contractData = (object) [
            'job_ref' => $contract->job_ref,
            'customer_name' => $contract->customer_name ?? 'Unknown',
            'status' => $contract->status
        ];

        $contract->delete();

        // Send email notification for contract deletion
        $this->notificationService->sendContractDeleted($contractData, auth()->user());

        return redirect()->route('contracts.index')->with('success', 'Contract deleted successfully.');
    }

    private function redirectAfterSave(Request $request, string $message): RedirectResponse
    {
        if ($request->input('return_to') === 'data-entry') {
            return redirect()->route('data-entry')->with('success', $message);
        }

        return redirect()->route('contracts.index')->with('success', $message);
    }

    private function resolveArea(string $customerName, ?string $area = null): ?string
    {
        if ($area) {
            return $area;
        }

        return Customer::where('name', $customerName)->value('region');
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
}
