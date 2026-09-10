<x-app-layout>
    <style>
        .section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
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
        .tag-expiring { background:var(--warning-bg); color:var(--warning); }
        .tag-expired { background:var(--danger-bg); color:var(--danger); }
        .tag-cancelled { background:#EFE9F7; color:#6B3FA0; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:8px 16px; border-radius:8px; }
        .btn-ef-primary:hover { background:#ff5b1f; color:#1a1300; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:8px 14px; border-radius:8px; }
        .btn-ef-outline:hover { background:var(--paper); }
        .filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px; }
        .filter-bar .form-select, .filter-bar .form-control { font-size:12.5px; border-radius:8px; border:1px solid var(--line); padding:7px 10px; }
        .pill-tab { padding:6px 14px; border-radius:20px; font-size:12.5px; font-weight:600; color:var(--ink-soft); cursor:pointer; border:1px solid transparent; }
        .pill-tab.active { background:var(--navy); color:#fff; }
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
        
        /* Pagination Styles */
        .pagination { display:flex; gap:4px; align-items:center; justify-content:center; margin-top:20px; }
        .pagination .page-link { 
            border:1px solid var(--line); 
            border-radius:6px; 
            padding:6px 12px; 
            font-size:12px; 
            font-weight:600; 
            color:var(--steel); 
            background:#fff; 
            text-decoration:none;
            transition:all 0.2s;
        }
        .pagination .page-link:hover { 
            background:var(--paper); 
            color:var(--navy); 
            border-color:var(--navy);
        }
        .pagination .page-item.active .page-link { 
            background:var(--navy); 
            color:#fff; 
            border-color:var(--navy); 
        }
        .pagination .page-item.disabled .page-link { 
            color:var(--ink-soft); 
            pointer-events:none; 
            opacity:0.5;
        }
    </style>

    <div class="section-head">
        <div>
            <h2>Contracts &amp; PPM Schedule</h2>
            <div class="section-sub">Every active installation and its next preventative maintenance due date</div>
        </div>
        <a href="{{ route('contracts.create') }}" class="btn btn-ef-primary"><i class="bi bi-plus-lg"></i> New Contract</a>
    </div>

    <div class="card p-3">
        <x-status-tabs :tabs="$statusTabs" :preserve="['search', 'area', 'frequency', 'date_from', 'date_to']" />

        <x-filter-form
            :action="route('contracts.index')"
            :keys="['search', 'status', 'area', 'frequency', 'date_from', 'date_to']"
            :total="$contracts->count()"
            label="contract"
            export-route="export.contracts"
        >
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <select name="area" class="form-select" style="width:160px;">
                <option value="">All areas</option>
                @foreach($areas as $areaOption)
                    <option value="{{ $areaOption }}" @selected(($filters['area'] ?? '') === $areaOption)>{{ $areaOption }}</option>
                @endforeach
            </select>
            <select name="frequency" class="form-select" style="width:170px;">
                <option value="">All frequencies</option>
                @foreach($frequencies as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['frequency'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control" style="width:150px;" title="Next PPM from">
            <span class="cell-sub">to</span>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control" style="width:150px;" title="Next PPM to">
            <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" style="width:200px;" placeholder="Search customer, site or ref…">
        </x-filter-form>

        <div class="table-responsive">
            <table class="ef-table">
                <thead>
                    <tr>
                        <th>Customer / Site</th>
                        <th>Area</th>
                        <th>Contract Ref</th>
                        <th>Frequency</th>
                        <th>Last PPM</th>
                        <th>Next PPM Due</th>
                        <th>Contract Expiry</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr>
                            <td>
                                <div class="cell-primary">{{ $contract->customer_name }}</div>
                                <div class="cell-sub">{{ $contract->site_name ?? '—' }}</div>
                            </td>
                            <td class="cell-sub">{{ $contract->area ?? '—' }}</td>
                            <td class="mono cell-sub">{{ $contract->contract_ref }}</td>
                            <td>{{ $contract->frequency }}</td>
                            <td class="mono cell-sub">{{ $contract->last_ppm_date ? $contract->last_ppm_date->format('d M Y') : '—' }}</td>
                            <td class="mono cell-primary">{{ $contract->next_ppm_due ? $contract->next_ppm_due->format('d M Y') : '—' }}</td>
                            <td class="mono cell-sub">{{ $contract->expiry_date ? $contract->expiry_date->format('d M Y') : '—' }}</td>
                            <td>
                                @php
                                    $status = $contract->status ?? 'upcoming';
                                    $statusClass = [
                                        'upcoming' => 'tag-upcoming',
                                        'overdue' => 'tag-overdue',
                                        'active' => 'tag-completed',
                                        'expiring' => 'tag-expiring',
                                        'expired' => 'tag-expired',
                                        'cancelled' => 'tag-cancelled'
                                    ][$status] ?? 'tag-upcoming';
                                    $label = [
                                        'upcoming' => 'Upcoming',
                                        'overdue' => 'Overdue',
                                        'active' => 'Active',
                                        'expiring' => 'Expiring',
                                        'expired' => 'Expired',
                                        'cancelled' => 'Cancelled'
                                    ][$status] ?? ucfirst($status);
                                @endphp
                                <span class="tag {{ $statusClass }}"><span>{{ $label }}</span></span>
                            </td>
                            <td>
                                <div class="crud-actions">
                                    <a class="btn btn-view" href="{{ route('contracts.show', $contract) }}" title="View"><i class="bi bi-eye"></i></a>
                                    <a class="btn btn-edit" href="{{ route('contracts.edit', $contract) }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-delete" onclick="deleteContract('{{ route('contracts.destroy', $contract) }}', '{{ $contract->contract_ref }}')" title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div style="padding:40px 20px;text-align:center;color:var(--ink-soft);">
                                    <i class="bi bi-file-earmark-text d-block mb-2" style="font-size:28px;color:var(--line);"></i>
                                    @if(request()->hasAny(['search', 'status', 'frequency', 'date_from', 'date_to']))
                                        No contracts match your filters. <a href="{{ route('contracts.index') }}">Clear filters</a>
                                    @else
                                        No contracts yet. Add your first contract to begin.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if ($contracts->hasPages())
            <div class="pagination">
                @if ($contracts->onFirstPage())
                    <span class="page-item disabled"><span class="page-link">Previous</span></span>
                @else
                    <a class="page-link" href="{{ $contracts->previousPageUrl() }}">Previous</a>
                @endif
                
                @foreach ($contracts->getUrlRange(1, $contracts->lastPage()) as $url => $page)
                    @if ($page == $contracts->currentPage())
                        <span class="page-item active"><span class="page-link">{{ $page }}</span></span>
                    @else
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if ($contracts->hasMorePages())
                    <a class="page-link" href="{{ $contracts->nextPageUrl() }}">Next</a>
                @else
                    <span class="page-item disabled"><span class="page-link">Next</span></span>
                @endif
            </div>
        @endif
    </div>

    <script>
        function deleteContract(url, contractRef) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Contract?',
                    text: 'Are you sure you want to delete contract ' + contractRef + '? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF6B35',
                    cancelButtonColor: '#13315C',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitDeleteForm(url);
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert is not loaded
                if (confirm('Delete this contract?')) {
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
