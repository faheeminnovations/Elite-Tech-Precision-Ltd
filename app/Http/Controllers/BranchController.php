<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id', 'region_id', 'status']);

        $branches = Branch::query()
            ->with(['customer', 'region'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('branch_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->when($filters['customer_id'] ?? null, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($filters['region_id'] ?? null, function ($query, $regionId) {
                $query->where('region_id', $regionId);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->onEachSide(1);

        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $regions = Region::orderBy('name')->get(['id', 'name']);

        return view('branches.index', [
            'branches' => $branches,
            'filters' => $filters,
            'customers' => $customers,
            'regions' => $regions,
        ]);
    }

    public function create(Request $request): View
    {
        $customerId = $request->query('customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;

        $customers = Customer::orderBy('name')->get(['id', 'name', 'region']);
        $regions = Region::orderBy('name')->get(['id', 'name']);

        return view('branches.create', [
            'customer' => $customer,
            'customers' => $customers,
            'regions' => $regions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'branch_name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate branch name under the same customer
        $duplicate = Branch::where('customer_id', $validated['customer_id'])
            ->where('branch_name', $validated['branch_name'])
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['branch_name' => 'A branch with this name already exists for this customer.']);
        }

        Branch::create($validated);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch added successfully.');
    }

    public function show(Branch $branch): View
    {
        $branch->load(['customer', 'region', 'doors', 'contracts', 'services']);

        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch): View
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'region']);
        $regions = Region::orderBy('name')->get(['id', 'name']);

        return view('branches.edit', [
            'branch' => $branch,
            'customers' => $customers,
            'regions' => $regions,
        ]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'branch_name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate branch name under the same customer (excluding current branch)
        $duplicate = Branch::where('customer_id', $validated['customer_id'])
            ->where('branch_name', $validated['branch_name'])
            ->where('id', '!=', $branch->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['branch_name' => 'A branch with this name already exists for this customer.']);
        }

        $branch->update($validated);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        // Check if branch has dependent records
        if ($branch->doors()->count() > 0) {
            return back()->with('error', 'Cannot delete branch because it has doors associated with it.');
        }

        if ($branch->contracts()->count() > 0) {
            return back()->with('error', 'Cannot delete branch because it has contracts associated with it.');
        }

        if ($branch->services()->count() > 0) {
            return back()->with('error', 'Cannot delete branch because it has services associated with it.');
        }

        $branch->delete();

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
