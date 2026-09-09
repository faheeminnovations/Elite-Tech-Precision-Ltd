<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Mail\StatusUpdateMail;
use App\Models\Contract;
use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CustomerController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'category', 'region']);

        $customers = Customer::query()
            ->filter($filters)
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->onEachSide(1);

        $regions = Customer::query()
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return view('customers.index', [
            'customers' => $customers,
            'filters' => $filters,
            'regions' => $regions,
            'statuses' => Customer::STATUSES,
            'categories' => Customer::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request)
    {
        $validated = $request->validated();

        $customer = Customer::create($validated);

        // Send email notification for customer creation
        $this->notificationService->sendCustomerCreated($customer, auth()->user());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully.',
                'redirect' => route('customers.index')
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'region' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]*$/'],
            'category' => ['nullable', 'string', 'in:new,chain'],
        ]);

        $customer = Customer::create($validated);

        // Send email notification for customer creation
        $this->notificationService->sendCustomerCreated($customer, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Customer added successfully.',
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'region' => $customer->region,
            ]
        ]);
    }

    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();

        $oldStatus = $customer->status;
        $newStatus = $validated['status'] ?? $customer->status;

        $customer->update($validated);

        if ($customer->wasChanged('region')) {
            Contract::where('customer_name', $customer->name)->update(['area' => $customer->region]);
        }

        // Send email notification for customer update
        $this->notificationService->sendCustomerUpdated($customer, auth()->user());

        // Send email notification if status changed
        if ($oldStatus !== $newStatus) {
            $this->notificationService->sendCustomerStatusChanged($customer, $oldStatus, $newStatus, auth()->user());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.',
                'redirect' => route('customers.index')
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customerData = (object) [
            'name' => $customer->name,
            'status' => $customer->status
        ];

        $customer->delete();

        // Send email notification for customer deletion
        $this->notificationService->sendCustomerDeleted($customerData, auth()->user());

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
