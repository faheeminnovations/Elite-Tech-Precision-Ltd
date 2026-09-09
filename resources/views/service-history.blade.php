<x-app-layout>
    <div class="card ef p-3">
        <div class="section-head" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2>Completed PPM Records & Service History</h2>
                <div class="section-sub">Permanent record of every completed visit, job sheet and recommendation</div>
            </div>
            <a href="{{ route('services.create') }}" class="btn btn-ef-primary"><i class="bi bi-check2-circle me-1"></i> Contract & PPM Service</a>
        </div>

        <x-filter-form
            :action="route('service-history')"
            :keys="['search', 'engineer', 'date_from', 'date_to']"
            :total="$services->total()"
            label="record"
            class="mt-2"
            export-route="export.service-history"
        >
            <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" style="width:220px;" placeholder="Search customer, site or job sheet #">
            <select name="engineer" class="form-select" style="width:170px;">
                <option value="">All engineers</option>
                @foreach($engineers as $engineer)
                    <option value="{{ $engineer->name }}" @selected(($filters['engineer'] ?? '') === $engineer->name)>{{ $engineer->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control" style="width:150px;" title="Completed from">
            <span class="cell-sub">to</span>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control" style="width:150px;" title="Completed to">
        </x-filter-form>

        <div class="table-responsive">
            <table class="ef-table">
                <thead><tr><th>Job Sheet</th><th>Customer / Site</th><th>Engineer</th><th>Completed</th><th>Recommendations</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td class="mono cell-primary">{{ $service->job_ref }}</td>
                            <td>
                                <div class="cell-primary">{{ $service->customer_name }}</div>
                                <div class="cell-sub">{{ $service->site_installation ?? '—' }}</div>
                            </td>
                            <td>
                                @php
                                    $engineerName = $service->engineer?->name ?? $service->engineer_name ?? '—';
                                @endphp
                                <span class="avatar-sm" style="width:26px;height:26px;border-radius:6px;background:var(--steel-light);color:#fff;font-size:10.5px;font-weight:600;display:inline-flex;align-items:center;justify-content:center;font-family:var(--disp);">{{ strtoupper(substr($engineerName !== '—' ? $engineerName : 'U', 0, 1)) }}</span>
                                <span class="cell-sub">{{ $engineerName }}</span>
                            </td>
                            <td class="mono cell-sub">{{ $service->visit_date ? $service->visit_date->format('d/m/Y') : '—' }}</td>
                            <td class="cell-sub">{{ Str::limit($service->recommendations ?? '—', 60) }}</td>
                            <td>
                                @php
                                    $statusMap = ['scheduled'=>'tag-upcoming','in-progress'=>'tag-pending','completed'=>'tag-completed','cancelled'=>'tag-declined'];
                                    $statusLabel = ucfirst(str_replace('-', ' ', $service->status));
                                @endphp
                                <span class="tag {{ $statusMap[$service->status] ?? 'tag-awaiting' }}"><span>{{ $statusLabel }}</span></span>
                            </td>
                            <td>
                                <div class="crud-actions">
                                    <a href="{{ route('services.show', $service) }}" class="btn btn-view" title="View"><i class="bi bi-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
                            <div class="empty-note">
                                <i class="bi bi-clipboard2-check d-block mb-2"></i>
                                @if(request()->hasAny(['search', 'engineer', 'date_from', 'date_to']))
                                    No completed records match your filters. <a href="{{ route('service-history') }}">Clear filters</a>
                                @else
                                    No completed service records yet.
                                @endif
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="mt-3">{{ $services->links() }}</div>
        @endif
    </div>
</x-app-layout>
