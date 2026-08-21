<x-app-layout>
    <div class="card ef p-3 mb-3">
        <div class="section-head">
            <div>
                <h2>Reports & Excel Export</h2>
                <div class="section-sub">Ireland-ready management, operations and customer reporting</div>
            </div>
            <span class="tag tag-upcoming"><span>EUR · Dublin</span></span>
        </div>
        <div class="report-builder">
            <form method="GET" action="{{ route('reports') }}" id="reportForm">
                <div class="row g-2 align-items-end mb-2">
                    <div class="col-12">
                        <label class="form-label">Report</label>
                        <select class="form-select" name="type" id="repType">
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}" @selected($type === $key)">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Area</label>
                        <select name="area" class="form-select">
                            <option value="">All areas</option>
                            @foreach($areas as $areaOption)
                                <option value="{{ $areaOption }}" @selected($area === $areaOption)">{{ $areaOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" @selected($category === $key)">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Start Date</label>
                        <input id="repStart" name="date_from" type="date" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">End Date</label>
                        <input id="repEnd" name="date_to" type="date" class="form-control" value="{{ $dateTo }}">
                    </div>
                </div>
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label">Customer</label>
                        <select id="repCustomer" name="customer" class="form-select">
                            <option value="">All customers</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" @selected($customer == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Engineer</label>
                        <select name="engineer" id="repEngineer" class="form-select">
                            <option value="">All</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}" @selected($engineer == $eng->id)">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">System</label>
                        <select name="system" class="form-select">
                            <option value="">All</option>
                            <option value="PPM" @selected($system === 'PPM')">PPM</option>
                            <option value="Repair" @selected($system === 'Repair')">Repair</option>
                            <option value="Installation" @selected($system === 'Installation')">Installation</option>
                            <option value="Service Call" @selected($system === 'Service Call')">Service Call</option>
                            <option value="Inspection" @selected($system === 'Inspection')">Inspection</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-ef-primary w-100"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @foreach([
            'area-ppm-schedule' => ['icon' => 'bi-geo-alt', 'title' => 'Area PPM Schedule', 'desc' => '3-week lookahead by area — chain and new customers.'],
            'upcoming-ppms' => ['icon' => 'bi-calendar-week', 'title' => 'Upcoming PPMs', 'desc' => 'PPMs due in the selected date range.'],
            'overdue-ppms' => ['icon' => 'bi-exclamation-triangle', 'title' => 'Overdue PPMs', 'desc' => 'Outstanding visits requiring action.'],
            'expiring-contracts' => ['icon' => 'bi-hourglass-split', 'title' => 'Contracts Expiring Soon', 'desc' => 'Renewal and retention planning.'],
            'customers-no-contract' => ['icon' => 'bi-person-x', 'title' => 'Customers Without Contract', 'desc' => 'Sales opportunity and follow-up list.'],
            'customer-responses' => ['icon' => 'bi-envelope-check', 'title' => 'Customer Responses', 'desc' => 'Accepted and declined PPM responses.'],
            'service-history' => ['icon' => 'bi-clipboard2-check', 'title' => 'Full Service History', 'desc' => 'Completed jobs, reports and recommendations.'],
            'service-jobs' => ['icon' => 'bi-wrench-adjustable', 'title' => 'Service Jobs', 'desc' => 'Scheduled, active and completed service work.'],
            'contracts-register' => ['icon' => 'bi-file-earmark-text', 'title' => 'Contracts Register', 'desc' => 'Contract status, expiry, frequency and next PPM.'],
            'customer-register' => ['icon' => 'bi-people', 'title' => 'Customer Register', 'desc' => 'Customer contacts, addresses and job references.'],
        ] as $reportKey => $card)
            <div class="col-md-6 col-xl-4">
                <div class="report-card" onclick="selectReport('{{ $reportKey }}')">
                    <i class="bi {{ $card['icon'] }} rep-icon"></i>
                    <h6>{{ $card['title'] }}</h6>
                    <p>{{ $card['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card ef p-3 mt-3">
        <div class="section-head">
            <div>
                <h2>Report Preview</h2>
                <div class="section-sub">Filtered results preview before Excel export</div>
            </div>
            <button class="btn btn-ef-outline" onclick="confirmExport()"><i class="bi bi-download"></i> Download Excel</button>
        </div>
        <div class="stat-strip mb-3">
            <div class="s"><b id="reportCount">{{ $preview['count'] }}</b><span>Rows</span></div>
            <div class="s"><b>DD/MM/YYYY</b><span>Date Format</span></div>
            <div class="s"><b>€ EUR</b><span>Currency</span></div>
            <div class="s"><b>Europe/Dublin</b><span>Timezone</span></div>
        </div>
        <div class="table-responsive">
            <table class="ef-table">
                <thead><tr><th>Report</th><th>Customer</th><th>Reference</th><th>Date</th><th>Status</th></tr></thead>
                <tbody id="reportPreviewBody">
                    @forelse($preview['rows'] as $row)
                        <tr>
                            <td>{{ $row['report'] }}</td>
                            <td class="cell-primary">{{ $row['customer'] }}</td>
                            <td class="mono">{{ $row['reference'] }}</td>
                            <td class="mono">
                                @if($type === 'area-ppm-schedule')
                                    {{ $row['date'] }}
                                @else
                                    {{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d/m/Y') : '—' }}
                                @endif
                            </td>
                            <td>{{ $row['status'] !== '—' ? \Illuminate\Support\Str::ucfirst($row['status']) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-note"><i class="bi bi-file-earmark-excel d-block mb-2"></i>No data available for the selected filters.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($preview['pagination']) && $preview['pagination']->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $preview['pagination']->firstItem() }} to {{ $preview['pagination']->lastItem() }} of {{ $preview['pagination']->total() }} entries
                </div>
                {{ $preview['pagination']->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>

    <script>
        function selectReport(type) {
            document.getElementById('repType').value = type;
            document.getElementById('reportForm').submit();
        }

        function confirmExport() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Export Report?',
                    text: 'Are you sure you want to export this report to Excel?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, export it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('reportForm');
                        const params = new URLSearchParams(new FormData(form));
                        const url = @json(route('export.reports')) + '?' + params.toString();
                        window.location.href = url;
                    }
                });
            } else {
                // Fallback to direct export if SweetAlert is not loaded
                const form = document.getElementById('reportForm');
                const params = new URLSearchParams(new FormData(form));
                const url = @json(route('export.reports')) + '?' + params.toString();
                window.location.href = url;
            }
        }

        // Initialize select2 for dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#repCustomer').select2({
                    placeholder: 'All customers',
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap-5',
                    dropdownParent: $(document.body),
                    minimumResultsForSearch: 3
                });

                $('#repEngineer').select2({
                    placeholder: 'All engineers',
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap-5',
                    dropdownParent: $(document.body),
                    minimumResultsForSearch: 3
                });
            }
        });
    </script>
</x-app-layout>
