<x-app-layout>
    <style>
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .form-label { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .title { font-family:Barlow Condensed,sans-serif; font-size:28px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .sub { font-size:13px; color:var(--ink-soft); }
        .info-row { display:flex; margin-bottom:12px; }
        .info-label { width:150px; font-weight:600; color:var(--ink-soft); font-size:13px; }
        .info-value { flex:1; font-size:13px; }
        .badge { font-size:11px; padding:4px 8px; border-radius:4px; }
        .badge-active { background:#d4edda; color:#155724; }
        .badge-inactive { background:#f8d7da; color:#721c24; }
    </style>
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="title">{{ $branch->branch_name }}</div>
                <div class="sub">Branch details and information</div>
            </div>
            <div>
                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-ef-outline me-2">Edit</a>
                <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ef-outline" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Branch Information</h6>
                <div class="info-row">
                    <div class="info-label">Branch Name:</div>
                    <div class="info-value">{{ $branch->branch_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Customer:</div>
                    <div class="info-value">
                        @if($branch->customer)
                            <a href="{{ route('customers.show', $branch->customer) }}">{{ $branch->customer->name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Region:</div>
                    <div class="info-value">{{ $branch->region->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">City:</div>
                    <div class="info-value">{{ $branch->city ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $branch->address ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $branch->status }}">
                            {{ ucfirst($branch->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Contact Information</h6>
                <div class="info-row">
                    <div class="info-label">Contact Person:</div>
                    <div class="info-value">{{ $branch->contact_person ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Contact Number:</div>
                    <div class="info-value">{{ $branch->contact_number ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">
                        @if($branch->email)
                            <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Doors ({{ $branch->doors->count() }})</h6>
                @if($branch->doors->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($branch->doors->take(5) as $door)
                            <li class="mb-2">
                                <a href="{{ route('doors.show', $door) }}">{{ $door->door_name }}</a>
                                <span class="text-muted">({{ $door->status }})</span>
                            </li>
                        @endforeach
                        @if($branch->doors->count() > 5)
                            <li class="text-muted">and {{ $branch->doors->count() - 5 }} more...</li>
                        @endif
                    </ul>
                    <a href="{{ route('doors.create', ['branch_id' => $branch->id]) }}" class="btn btn-sm btn-ef-outline mt-2">Add Door</a>
                @else
                    <p class="text-muted">No doors for this branch</p>
                    <a href="{{ route('doors.create', ['branch_id' => $branch->id]) }}" class="btn btn-sm btn-ef-outline mt-2">Add Door</a>
                @endif
            </div>

            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Related Contracts ({{ $branch->contracts->count() }})</h6>
                @if($branch->contracts->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($branch->contracts->take(5) as $contract)
                            <li class="mb-2">
                                <a href="{{ route('contracts.show', $contract) }}">{{ $contract->contract_ref ?? 'Contract #' . $contract->id }}</a>
                                <span class="text-muted">({{ $contract->status }})</span>
                            </li>
                        @endforeach
                        @if($branch->contracts->count() > 5)
                            <li class="text-muted">and {{ $branch->contracts->count() - 5 }} more...</li>
                        @endif
                    </ul>
                @else
                    <p class="text-muted">No contracts for this branch</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('branches.index') }}" class="btn btn-ef-outline">Back to Branches</a>
        </div>
    </div>
</x-app-layout>
