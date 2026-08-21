<x-app-layout>
    <style>
        .section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
        .section-head h2 { font-size:19px; margin:0; color:var(--navy); font-family:var(--disp); }
        .section-sub { color:var(--ink-soft); font-size:13px; margin-top:2px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:12px; box-shadow:0 1px 2px rgba(11,37,69,0.03); }
        .tag { display:inline-flex; align-items:center; gap:5px; font-family:var(--mono); font-size:11px; font-weight:500; padding:3px 9px 3px 12px; position:relative; border-radius:2px 6px 6px 2px; line-height:1.6; white-space:nowrap; }
        .tag::before { content:''; position:absolute; left:4px; top:50%; transform:translateY(-50%); width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.7); }
        .tag::after { content:''; position:absolute; left:0; top:0; bottom:0; width:10px; background:inherit; clip-path:polygon(0 50%, 60% 0, 100% 0, 100% 100%, 60% 100%); }
        .tag span { position:relative; z-index:1; padding-left:2px; }
        .tag-upcoming { background:#E4EBF6; color:var(--steel); }
        .tag-overdue { background:var(--danger-bg); color:var(--danger); }
        .tag-completed { background:var(--success-bg); color:var(--success); }
        .tag-pending { background:var(--warning-bg); color:var(--warning); }
        .tag-nocontract { background:#EFE9F7; color:#6B3FA0; }
        .tag-accepted { background:var(--success-bg); color:var(--success); }
        .tag-declined { background:var(--danger-bg); color:var(--danger); }
        .tag-awaiting { background:#F1F1F1; color:#6b6b6b; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:8px 16px; border-radius:8px; }
        .btn-ef-primary:hover { background:#ff5b1f; color:#1a1300; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:8px 14px; border-radius:8px; }
        .btn-ef-outline:hover { background:var(--paper); }
        .filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px; }
        .filter-bar .form-select, .filter-bar .form-control { font-size:12.5px; border-radius:8px; border:1px solid var(--line); padding:7px 10px; }
        .cell-primary { font-weight:600; color:var(--navy); }
        .cell-sub { font-size:11.5px; color:var(--ink-soft); }
        .mono { font-family:var(--mono); }
        table.ef-table { width:100%; border-collapse:separate; border-spacing:0; font-size:13px; }
        table.ef-table thead th { text-align:left; font-size:10.8px; text-transform:uppercase; letter-spacing:0.7px; color:var(--ink-soft); font-weight:600; padding:9px 14px; border-bottom:1px solid var(--line); background:#FAFBFD; white-space:nowrap; }
        table.ef-table thead th:first-child { border-top-left-radius:10px; }
        table.ef-table thead th:last-child { border-top-right-radius:10px; }
        table.ef-table tbody td { padding:11px 14px; border-bottom:1px solid var(--line); vertical-align:middle; }
        table.ef-table tbody tr:last-child td { border-bottom:none; }
        table.ef-table tbody tr:hover { background:#FAFBFD; }
        .crud-actions { display:flex; gap:6px; justify-content:flex-end; }
        .crud-actions .btn { width:32px; height:32px; padding:0; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; border:none; font-size:14px; }
        .btn-view { background:var(--success-bg); color:var(--success); }
        .btn-edit { background:#E4EBF6; color:var(--steel); }
        .btn-delete { background:var(--danger-bg); color:var(--danger); }
        .phone-cell { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
    </style>

    <div class="section-head" style="margin-bottom:24px;">
        <div>
            <h2>Customers</h2>
            <div class="section-sub">EliteFlow customer register and maintenance status</div>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-ef-primary"><i class="bi bi-plus-lg"></i> Add Customer</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">{{ session('success') }}</div>
    @endif

    <div class="card p-3">
        <x-filter-form
            :action="route('customers.index')"
            :keys="['search', 'status', 'category', 'region']"
            :total="$customers->total()"
            label="customer"
            export-route="export.customers"
        >
            <select name="status" class="form-select" style="width:180px;">
                <option value="">All contract statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category" class="form-select" style="width:170px;">
                <option value="">All categories</option>
                @foreach($categories as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="region" class="form-select" style="width:160px;">
                <option value="">All areas</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" @selected(($filters['region'] ?? '') === $region)>{{ $region }}</option>
                @endforeach
            </select>
            <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" style="width:220px;" placeholder="Search customer or site…">
        </x-filter-form>

        <div class="table-responsive">
            <table class="ef-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Job Ref Number</th>
                            <th>Job Details</th>
                            <th>Date of Completion</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Area</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="cell-primary">{{ $customer->name }}</td>
                                <td>
                                    @php $cat = $customer->category ?? 'new'; @endphp
                                    <span class="tag {{ $cat === 'chain' ? 'tag-completed' : 'tag-upcoming' }}"><span>{{ $categories[$cat] ?? ucfirst($cat) }}</span></span>
                                </td>
                                <td class="mono cell-primary">{{ $customer->job_ref ?? '—' }}</td>
                                <td class="cell-sub">{{ $customer->job_details ?? '—' }}</td>
                                <td class="mono">{{ $customer->completion_date ? $customer->completion_date->format('d/m/Y') : '—' }}</td>
                                <td class="phone-cell">{{ $customer->phone ?? '—' }}</td>
                                <td class="cell-sub">{{ $customer->email ?? '—' }}</td>
                                <td>
                                    @php
                                        $status = $customer->status ?? 'new';
                                        $statusLabels = [
                                            'active' => ['tag-completed', 'Active'],
                                            'upcoming' => ['tag-upcoming', 'Upcoming'],
                                            'overdue' => ['tag-overdue', 'Overdue'],
                                            'expiring' => ['tag-pending', 'Expiring'],
                                            'nocontract' => ['tag-nocontract', 'No Contract'],
                                            'accepted' => ['tag-accepted', 'Accepted'],
                                            'declined' => ['tag-declined', 'Declined'],
                                            'awaiting' => ['tag-awaiting', 'Awaiting'],
                                            'new' => ['tag-upcoming', 'New'],
                                            'previous' => ['tag-nocontract', 'Previous'],
                                            'updated' => ['tag-pending', 'Updated'],
                                        ];
                                        [$cls, $label] = $statusLabels[$status] ?? ['tag-awaiting', ucfirst($status)];
                                    @endphp
                                    <span class="tag {{ $cls }}"><span>{{ $label }}</span></span>
                                </td>
                                <td class="cell-sub">{{ $customer->region ?? '—' }}</td>
                                <td>
                                    <div class="crud-actions">
                                        <a class="btn btn-view" href="{{ route('customers.show', $customer) }}" title="View"><i class="bi bi-eye"></i></a>
                                        <a class="btn btn-edit" href="{{ route('customers.edit', $customer) }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <button class="btn btn-delete" onclick="deleteCustomer('{{ route('customers.destroy', $customer) }}', '{{ $customer->name }}')" title="Delete"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-note">
                                        <i class="bi bi-people d-block mb-2" style="font-size:28px;color:var(--line);"></i>
                                        @if(request()->hasAny(['search', 'status', 'region']))
                                            No customers match your filters. <a href="{{ route('customers.index') }}">Clear filters</a>
                                        @else
                                            No customers yet. Add your first customer to begin.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>

        @if($customers->hasPages())
            {{ $customers->appends(request()->except('page'))->links('vendor.pagination.eliteflow') }}
        @endif
    </div>

    <script>
        function deleteCustomer(url, customerName) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Customer?',
                    text: 'Are you sure you want to delete customer ' + customerName + '? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitDeleteForm(url);
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert is not loaded
                if (confirm('Delete this customer?')) {
                    submitDeleteForm(url);
                }
            }
        }

        function submitDeleteForm(url) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.style.display = 'none';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-app-layout>
