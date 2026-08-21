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
        .tag-completed { background:#E4F5EC; color:#1E8E5A; }
        .tag-nocontract { background:#EFE9F7; color:#6B3FA0; }
        .tag-pending { background:#FFF1DE; color:#B96A00; }
        .btn-row { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
    </style>
    <div class="card">
        <div class="card">
            <div class="title">{{ $customer->name }}</div>
            <div class="sub">Customer account details and job information</div>
            <div class="mt-3">
                @php
                    $status = $customer->status ?? 'new';
                    $label = ['active' => 'Active', 'nocontract' => 'No Contract', 'expiring' => 'Expiring', 'new' => 'New', 'updated' => 'Updated', 'previous' => 'Previous'][$status] ?? ucfirst($status);
                    $class = ['active' => 'tag-completed', 'nocontract' => 'tag-nocontract', 'expiring' => 'tag-pending', 'new' => 'tag-completed', 'updated' => 'tag-pending', 'previous' => 'tag-nocontract'][$status] ?? 'tag-pending';
                @endphp
                <span class="tag {{ $class }}"><span>{{ $label }}</span></span>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <div class="meta">Job Ref</div>
                    <div class="value mono">{{ $customer->job_ref ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="meta">Completion Date</div>
                    <div class="value mono">{{ $customer->completion_date ? $customer->completion_date->format('d/m/Y') : '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="meta">Category</div>
                    <div class="value">{{ \App\Models\Customer::CATEGORIES[$customer->category ?? 'new'] ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="meta">Area</div>
                    <div class="value">{{ $customer->region ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="meta">Phone</div>
                    <div class="value">{{ $customer->phone ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="meta">Email</div>
                    <div class="value">{{ $customer->email ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="meta">Address</div>
                    <div class="value">{{ $customer->address ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="meta">Job Details</div>
                    <div class="value">{{ $customer->job_details ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="meta">Job Notes</div>
                    <div class="value">{{ $customer->job_notes ?? '—' }}</div>
                </div>
            </div>

            <div class="btn-row">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-ef-primary">Edit</a>
                <a href="{{ route('customers.index') }}" class="btn btn-ef-outline">Back to list</a>
            </div>
        </div>
    </div>
</x-app-layout>
