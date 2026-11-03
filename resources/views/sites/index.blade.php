<x-app-layout>
    <style>
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .form-label { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .form-control, .form-select { border-radius:8px; border:1px solid var(--line); font-size:13px; padding:9px 11px; }
        .title { font-family:Barlow Condensed,sans-serif; font-size:28px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .sub { font-size:13px; color:var(--ink-soft); }
        .table th { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .badge { font-size:11px; padding:4px 8px; border-radius:4px; }
        .badge-active { background:#d4edda; color:#155724; }
        .badge-inactive { background:#f8d7da; color:#721c24; }
    </style>
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="title">Sites</div>
                <div class="sub">Manage customer sites</div>
            </div>
            <a href="{{ route('sites.create') }}" class="btn btn-ef-primary">
                <i class="bi bi-plus-circle"></i> Add Site
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('sites.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search sites..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="customer_id">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ ($filters['customer_id'] ?? '') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="region_id">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ ($filters['region_id'] ?? '') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-ef-outline btn-sm">Filter</button>
                    <a href="{{ route('sites.index') }}" class="btn btn-ef-outline btn-sm">Clear</a>
                </div>
            </div>
        </form>

        <!-- Sites Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Site Name</th>
                        <th>Customer</th>
                        <th>Region</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                        <tr>
                            <td>
                                <strong>{{ $site->site_name }}</strong>
                            </td>
                            <td>{{ $site->customer->name ?? '-' }}</td>
                            <td>{{ $site->region->name ?? '-' }}</td>
                            <td>{{ $site->address ?? '-' }}</td>
                            <td>
                                @if($site->contact_person)
                                    <div>{{ $site->contact_person }}</div>
                                    @if($site->contact_number)
                                        <small class="text-muted">{{ $site->contact_number }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $site->status }}">
                                    {{ ucfirst($site->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-outline-primary me-1">View</a>
                                <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                                <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this site?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No sites found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $sites->links() }}
    </div>
</x-app-layout>
