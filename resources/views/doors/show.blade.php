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
                <div class="title">{{ $door->door_name }}</div>
                <div class="sub">Door details and information</div>
            </div>
            <div>
                <a href="{{ route('doors.edit', $door) }}" class="btn btn-ef-outline me-2">Edit</a>
                <form action="{{ route('doors.destroy', $door) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ef-outline" onclick="return confirm('Are you sure you want to delete this door?')">Delete</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Door Information</h6>
                <div class="info-row">
                    <div class="info-label">Door Name:</div>
                    <div class="info-value">{{ $door->door_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Customer:</div>
                    <div class="info-value">
                        @if($door->customerSite && $door->customerSite->customer)
                            <a href="{{ route('customers.show', $door->customerSite->customer) }}">{{ $door->customerSite->customer->name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Site:</div>
                    <div class="info-value">
                        @if($door->customerSite)
                            <a href="{{ route('sites.show', $door->customerSite) }}">{{ $door->customerSite->site_name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Region:</div>
                    <div class="info-value">{{ $door->customerSite->region->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Location Details:</div>
                    <div class="info-value">{{ $door->location_details ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $door->status }}">
                            {{ ucfirst($door->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Related Contracts ({{ $door->contracts->count() }})</h6>
                @if($door->contracts->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($door->contracts->take(5) as $contract)
                            <li class="mb-2">
                                <a href="{{ route('contracts.show', $contract) }}">{{ $contract->contract_ref ?? 'Contract #' . $contract->id }}</a>
                                <span class="text-muted">({{ $contract->status }})</span>
                            </li>
                        @endforeach
                        @if($door->contracts->count() > 5)
                            <li class="text-muted">and {{ $door->contracts->count() - 5 }} more...</li>
                        @endif
                    </ul>
                @else
                    <p class="text-muted">No contracts for this door</p>
                @endif
            </div>

            <div class="col-md-6">
                <h6 class="mb-3" style="font-size: 14px; font-weight: 700; color: var(--navy);">Related Services ({{ $door->services->count() }})</h6>
                @if($door->services->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($door->services->take(5) as $service)
                            <li class="mb-2">
                                <a href="{{ route('services.show', $service) }}">{{ $service->job_ref ?? 'Service #' . $service->id }}</a>
                                <span class="text-muted">({{ $service->status }})</span>
                            </li>
                        @endforeach
                        @if($door->services->count() > 5)
                            <li class="text-muted">and {{ $door->services->count() - 5 }} more...</li>
                        @endif
                    </ul>
                @else
                    <p class="text-muted">No services for this door</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('doors.index') }}" class="btn btn-ef-outline">Back to Doors</a>
        </div>
    </div>
</x-app-layout>
