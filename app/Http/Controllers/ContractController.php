<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Mail\ContractStatusMail;
use App\Models\Contract;
use App\Models\Customer;
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
            ->get();

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
        return view('contracts.create', [
            'customers' => Customer::orderBy('name')->get(['name', 'region', 'category', 'email']),
            'areas' => Customer::AREAS,
        ]);
    }

    public function store(ContractRequest $request)
    {
        $validated = $request->validated();
        $validated['area'] = $this->resolveArea($validated['customer_name'], $validated['area'] ?? null);

        // Set default values for fields that have database constraints but are optional in form
        $validated['frequency'] = $validated['frequency'] ?? '6 monthly';
        $validated['status'] = $validated['status'] ?? 'upcoming';

        $contract = Contract::create($validated);

        // Send email notification for contract creation
        $this->notificationService->sendContractCreated($contract, auth()->user());

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
        return view('contracts.edit', [
            'contract' => $contract,
            'customers' => Customer::orderBy('name')->get(['name', 'region', 'category', 'email']),
            'areas' => Customer::AREAS,
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

        $oldStatus = $contract->status;
        $newStatus = $validated['status'] ?? $contract->status;

        $contract->update($validated);

        // Send email notification for contract update
        $this->notificationService->sendContractUpdated($contract, auth()->user());

        // Send email notification if status changed
        if ($oldStatus !== $newStatus) {
            $this->notificationService->sendContractStatusChanged($contract, $oldStatus, $newStatus, auth()->user());
        }

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
}
