<x-app-layout>
    <style>
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
        .title { font-family:Barlow Condensed,sans-serif; font-size:28px; font-weight:700; color:var(--navy); margin-bottom:8px; }
        .sub { font-size:13px; color:var(--ink-soft); }
        .meta { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .value { font-size:15px; margin-top:4px; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .tag { display:inline-flex; align-items:center; gap:5px; font-family:var(--mono); font-size:11px; font-weight:500; padding:3px 9px 3px 12px; position:relative; border-radius:2px 6px 6px 2px; line-height:1.6; white-space:nowrap; }
        .tag::before { content:''; position:absolute; left:4px; top:50%; transform:translateY(-50%); width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.7); }
        .tag::after { content:''; position:absolute; left:0; top:0; bottom:0; width:10px; background:inherit; clip-path:polygon(0 50%, 60% 0, 100% 0, 100% 100%, 60% 100%); }
        .tag span { position:relative; z-index:1; padding-left:2px; }
        .tag-upcoming { background:#E4EBF6; color:var(--steel); }
        .tag-overdue { background:var(--danger-bg); color:var(--danger); }
        .tag-active { background:var(--success-bg); color:var(--success); }
        .tag-expiring { background:var(--warning-bg); color:var(--warning); }
        .btn-row { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
        .mono { font-family:var(--mono); }
    </style>
    <div class="card">
        <div class="title">{{ $contract->customer_name }}</div>
        <div class="sub">Contract {{ $contract->contract_ref }}</div>
        <div class="mt-3">
            @php
                $status = $contract->status ?? 'upcoming';
                $statusClass = ['upcoming' => 'tag-upcoming', 'overdue' => 'tag-overdue', 'active' => 'tag-active', 'expiring' => 'tag-expiring'][$status] ?? 'tag-upcoming';
                $label = ['upcoming' => 'Upcoming', 'overdue' => 'Overdue', 'active' => 'Active', 'expiring' => 'Expiring'][$status] ?? ucfirst($status);
            @endphp
            <span class="tag {{ $statusClass }}"><span>{{ $label }}</span></span>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <div class="meta">Site / Installation</div>
                <div class="value">{{ $contract->site_name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="meta">Frequency</div>
                <div class="value">{{ $contract->frequency }}</div>
            </div>
            <div class="col-md-4">
                <div class="meta">Status</div>
                <div class="value">{{ ucfirst($contract->status) }}</div>
            </div>
            <div class="col-md-3">
                <div class="meta">Last PPM</div>
                <div class="value mono">{{ $contract->last_ppm_date ? $contract->last_ppm_date->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="meta">Next PPM Due</div>
                <div class="value mono">{{ $contract->next_ppm_due ? $contract->next_ppm_due->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="meta">Contract Expiry</div>
                <div class="value mono">{{ $contract->expiry_date ? $contract->expiry_date->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="meta">Start Date</div>
                <div class="value mono">{{ $contract->start_date ? $contract->start_date->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="meta">Contract Value</div>
                <div class="value">€ {{ number_format($contract->contract_value ?? 0, 2) }}</div>
            </div>
            <div class="col-md-6">
                <div class="meta">Customer Email</div>
                <div class="value">{{ $contract->customer_email ?? '—' }}</div>
            </div>
            <div class="col-12">
                <div class="meta">Contract Notes</div>
                <div class="value">{{ $contract->notes ?? '—' }}</div>
            </div>
        </div>

        <div class="btn-row">
            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-ef-primary">Edit</a>
            <a href="{{ route('contracts.index') }}" class="btn btn-ef-outline">Back to list</a>
        </div>
    </div>
</x-app-layout>
