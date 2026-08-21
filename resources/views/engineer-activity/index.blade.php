<x-app-layout>
    <div class="section-head" style="margin-bottom:24px;">
        <div>
            <h2>Engineer Activity Portal</h2>
            <div class="section-sub">Monitor engineer logins, job activity, and portal usage</div>
        </div>
        <a href="{{ route('reports', ['type' => 'engineer-performance']) }}" class="btn btn-ef-outline"><i class="bi bi-file-earmark-bar-graph"></i> Engineer Reports</a>
    </div>

    <div class="row g-3 mb-3">
        @foreach($engineerSummaries as $summary)
            <div class="col-md-4">
                <div class="kpi k-blue">
                    <div class="label">{{ $summary['engineer']->name }}</div>
                    <div class="value">{{ $summary['total_jobs'] }}</div>
                    <div class="meta">
                        <span class="me-2"><i class="bi bi-check-circle"></i>{{ $summary['completed'] }} completed</span>
                        <span><i class="bi bi-calendar"></i>{{ $summary['scheduled'] }} scheduled</span>
                    </div>
                    <div class="meta mt-1">
                        Last login: {{ $summary['last_login']?->format('d/m/Y H:i') ?? 'Never' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card ef p-3 mb-3">
        <form method="GET" action="{{ route('admin.engineer-activity') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Engineer</label>
                <select name="engineer_id" class="form-select">
                    <option value="">All engineers</option>
                    @foreach($engineers as $engineer)
                        <option value="{{ $engineer->id }}" @selected(($filters['engineer_id'] ?? '') == $engineer->id)>{{ $engineer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Module</label>
                <select name="module" class="form-select">
                    <option value="">All modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" @selected(($filters['module'] ?? '') === $mod)>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-ef-outline w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <div class="card ef p-3">
        <div class="section-head mb-3">
            <h2>Activity Log</h2>
            <div class="section-sub">{{ $activities->total() }} recorded actions</div>
        </div>
        <div class="table-responsive">
            <table class="ef-table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Engineer</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td class="mono cell-sub">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                            <td class="cell-primary">{{ $activity->user?->name ?? '—' }}</td>
                            <td><span class="tag tag-upcoming"><span>{{ $activity->action }}</span></span></td>
                            <td class="cell-sub">{{ ucfirst($activity->module ?? '—') }}</td>
                            <td>{{ $activity->description ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center cell-sub py-4">No engineer activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
            <div class="mt-3">{{ $activities->links() }}</div>
        @endif
    </div>
</x-app-layout>
