<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
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

        Customer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully.',
                'redirect' => route('customers.index')
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
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

        $customer->update($validated);

        if ($customer->wasChanged('region')) {
            Contract::where('customer_name', $customer->name)->update(['area' => $customer->region]);
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
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
