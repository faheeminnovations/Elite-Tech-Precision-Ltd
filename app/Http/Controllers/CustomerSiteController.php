<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSite;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerSiteController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id', 'region_id', 'status']);

        $sites = CustomerSite::query()
            ->with(['customer', 'region'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('site_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
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

        return view('sites.index', [
            'sites' => $sites,
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

        return view('sites.create', [
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
            'site_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate site name under the same customer and region
        $duplicate = CustomerSite::where('customer_id', $validated['customer_id'])
            ->where('site_name', $validated['site_name'])
            ->when($validated['region_id'] ?? null, function ($query, $regionId) {
                $query->where('region_id', $regionId);
            })
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['site_name' => 'A site with this name already exists for this customer in the selected region.']);
        }

        CustomerSite::create($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site added successfully.');
    }

    public function show(CustomerSite $site): View
    {
        $site->load(['customer', 'region', 'contracts', 'services']);

        return view('sites.show', compact('site'));
    }

    public function edit(CustomerSite $site): View
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'region']);
        $regions = Region::orderBy('name')->get(['id', 'name']);

        return view('sites.edit', [
            'site' => $site,
            'customers' => $customers,
            'regions' => $regions,
        ]);
    }

    public function update(Request $request, CustomerSite $site): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'site_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate site name under the same customer and region (excluding current site)
        $duplicate = CustomerSite::where('customer_id', $validated['customer_id'])
            ->where('site_name', $validated['site_name'])
            ->when($validated['region_id'] ?? null, function ($query, $regionId) {
                $query->where('region_id', $regionId);
            })
            ->where('id', '!=', $site->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['site_name' => 'A site with this name already exists for this customer in the selected region.']);
        }

        $site->update($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    public function destroy(CustomerSite $site): RedirectResponse
    {
        $site->delete();

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site deleted successfully.');
    }
}
