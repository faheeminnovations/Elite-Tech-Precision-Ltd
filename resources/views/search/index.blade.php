<x-app-layout>
    <div class="section-head" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h2 style="font-size:19px;margin:0;color:var(--navy);font-family:var(--disp);">Search Results</h2>
            <div class="section-sub" style="color:var(--ink-soft);font-size:13px;margin-top:2px;">
                @if(strlen($query) >= 2)
                    {{ $total }} {{ Str::plural('result', $total) }} for “{{ $query }}”
                @else
                    Enter at least 2 characters to search across customers, contracts, services and responses
                @endif
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('search') }}" class="filter-bar mb-3" style="display:flex;gap:10px;align-items:center;">
        <input type="text" name="q" value="{{ $query }}" class="form-control" style="max-width:360px;" placeholder="Search customers, sites, job sheets…" minlength="2" autofocus>
        <button type="submit" class="btn-ef-primary" style="background:var(--orange);border:none;color:#1a1300;font-weight:600;font-size:13px;padding:8px 16px;border-radius:8px;">
            <i class="bi bi-search"></i> Search
        </button>
    </form>

    @if(strlen($query) < 2)
        <div class="card ef p-4 text-center cell-sub">
            <i class="bi bi-search d-block mb-2" style="font-size:28px;"></i>
            Use the search box above or the topbar to find customers, contracts, service jobs and customer responses.
        </div>
    @else
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card ef p-3 h-100">
                    <div class="section-head" style="margin-bottom:12px;">
                        <h3 style="font-size:16px;margin:0;color:var(--navy);">Customers</h3>
                        <span class="cell-sub">{{ $results['customers']->count() }}</span>
                    </div>
                    @forelse($results['customers'] as $customer)
                        <a href="{{ route('customers.show', $customer) }}" class="search-result-item">
                            <div class="cell-primary">{{ $customer->name }}</div>
                            <div class="cell-sub">{{ $customer->job_ref }} · {{ $customer->email ?: 'No email' }}</div>
                        </a>
                    @empty
                        <div class="cell-sub">No customers found.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card ef p-3 h-100">
                    <div class="section-head" style="margin-bottom:12px;">
                        <h3 style="font-size:16px;margin:0;color:var(--navy);">Contracts</h3>
                        <span class="cell-sub">{{ $results['contracts']->count() }}</span>
                    </div>
                    @forelse($results['contracts'] as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="search-result-item">
                            <div class="cell-primary">{{ $contract->customer_name }}</div>
                            <div class="cell-sub">{{ $contract->contract_ref }} · {{ $contract->site_name }}</div>
                        </a>
                    @empty
                        <div class="cell-sub">No contracts found.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card ef p-3 h-100">
                    <div class="section-head" style="margin-bottom:12px;">
                        <h3 style="font-size:16px;margin:0;color:var(--navy);">Service Jobs</h3>
                        <span class="cell-sub">{{ $results['services']->count() }}</span>
                    </div>
                    @forelse($results['services'] as $service)
                        <a href="{{ route('services.show', $service) }}" class="search-result-item">
                            <div class="cell-primary">{{ $service->job_ref }} — {{ $service->customer_name }}</div>
                            <div class="cell-sub">{{ $service->site_installation }} · {{ $service->visit_date?->format('d/m/Y') ?? '—' }}</div>
                        </a>
                    @empty
                        <div class="cell-sub">No service jobs found.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card ef p-3 h-100">
                    <div class="section-head" style="margin-bottom:12px;">
                        <h3 style="font-size:16px;margin:0;color:var(--navy);">Customer Responses</h3>
                        <span class="cell-sub">{{ $results['responses']->count() }}</span>
                    </div>
                    @forelse($results['responses'] as $response)
                        <a href="{{ route('responses.show', $response) }}" class="search-result-item">
                            <div class="cell-primary">{{ $response->customer_name }}</div>
                            <div class="cell-sub">{{ $response->site_name }} · {{ ucfirst($response->response) }}</div>
                        </a>
                    @empty
                        <div class="cell-sub">No responses found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <style>
        .search-result-item {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
            text-decoration: none;
            color: inherit;
        }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #FAFBFD; margin: 0 -12px; padding-left: 12px; padding-right: 12px; }
        .cell-primary { font-weight: 600; color: var(--navy); font-size: 13.5px; }
        .cell-sub { font-size: 12px; color: var(--ink-soft); }
    </style>
</x-app-layout>
