<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSite;
use App\Models\Door;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id', 'customer_site_id', 'status']);

        $doors = Door::query()
            ->with(['customerSite', 'customerSite.customer', 'customerSite.region'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('door_name', 'like', "%{$search}%")
                    ->orWhere('location_details', 'like', "%{$search}%");
            })
            ->when($filters['customer_id'] ?? null, function ($query, $customerId) {
                $query->whereHas('customerSite', function ($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                });
            })
            ->when($filters['customer_site_id'] ?? null, function ($query, $siteId) {
                $query->where('customer_site_id', $siteId);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->onEachSide(1);

        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $customerSites = CustomerSite::with('customer')->orderBy('site_name')->get(['id', 'site_name', 'customer_id']);

        return view('doors.index', [
            'doors' => $doors,
            'filters' => $filters,
            'customers' => $customers,
            'customerSites' => $customerSites,
        ]);
    }

    public function create(Request $request): View
    {
        $customerId = $request->query('customer_id');
        $customerSiteId = $request->query('customer_site_id');

        $customer = $customerId ? Customer::find($customerId) : null;
        $customerSite = $customerSiteId ? CustomerSite::find($customerSiteId) : null;

        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $customerSites = CustomerSite::with('customer')->orderBy('site_name')->get(['id', 'site_name', 'customer_id']);

        return view('doors.create', [
            'customer' => $customer,
            'customerSite' => $customerSite,
            'customers' => $customers,
            'customerSites' => $customerSites,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_site_id' => ['required', 'exists:customer_sites,id'],
            'door_name' => ['required', 'string', 'max:255'],
            'location_details' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate door name under the same customer site
        $duplicate = Door::where('customer_site_id', $validated['customer_site_id'])
            ->where('door_name', $validated['door_name'])
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['door_name' => 'A door with this name already exists for this site.']);
        }

        Door::create($validated);

        return redirect()
            ->route('doors.index')
            ->with('success', 'Door added successfully.');
    }

    public function show(Door $door): View
    {
        $door->load(['customerSite', 'customerSite.customer', 'customerSite.region', 'contracts', 'services']);

        return view('doors.show', compact('door'));
    }

    public function edit(Door $door): View
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $customerSites = CustomerSite::with('customer')->orderBy('site_name')->get(['id', 'site_name', 'customer_id']);

        return view('doors.edit', [
            'door' => $door,
            'customers' => $customers,
            'customerSites' => $customerSites,
        ]);
    }

    public function update(Request $request, Door $door): RedirectResponse
    {
        $validated = $request->validate([
            'customer_site_id' => ['required', 'exists:customer_sites,id'],
            'door_name' => ['required', 'string', 'max:255'],
            'location_details' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check for duplicate door name under the same customer site (excluding current door)
        $duplicate = Door::where('customer_site_id', $validated['customer_site_id'])
            ->where('door_name', $validated['door_name'])
            ->where('id', '!=', $door->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['door_name' => 'A door with this name already exists for this site.']);
        }

        $door->update($validated);

        return redirect()
            ->route('doors.index')
            ->with('success', 'Door updated successfully.');
    }

    public function destroy(Door $door): RedirectResponse
    {
        // Check if door has dependent records
        if ($door->contracts()->count() > 0) {
            return back()->with('error', 'Cannot delete door because it has contracts associated with it.');
        }

        if ($door->services()->count() > 0) {
            return back()->with('error', 'Cannot delete door because it has services associated with it.');
        }

        $door->delete();

        return redirect()
            ->route('doors.index')
            ->with('success', 'Door deleted successfully.');
    }
}
