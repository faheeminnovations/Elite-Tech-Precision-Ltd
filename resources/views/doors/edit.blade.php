<x-app-layout>
    <style>
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .form-label { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .form-control, .form-select { border-radius:8px; border:1px solid var(--line); font-size:13px; padding:9px 11px; }
        .title { font-family:Barlow Condensed,sans-serif; font-size:28px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .sub { font-size:13px; color:var(--ink-soft); }
        .btn-row { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
        .site-context { background:#f8f9fa; border:1px solid var(--line); border-radius:8px; padding:16px; margin-bottom:20px; }
    </style>
    <div class="card">
        <div class="title">Edit Door / Location</div>
        <div class="sub">Update door information</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('doors.update', $door) }}" class="mt-4">
            @csrf
            @method('PUT')

            <!-- Customer Site Selection -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Customer Site <span class="text-danger">*</span></label>
                    <select class="form-select" name="customer_site_id" id="siteSelect" required onchange="loadSiteDetails()">
                        <option value="">Select a site</option>
                        @foreach($customerSites as $site)
                            <option value="{{ $site->id }}"
                                    data-customer="{{ $site->customer->name }}"
                                    data-region="{{ $site->region->name ?? '-' }}"
                                    {{ $door->customer_site_id == $site->id ? 'selected' : '' }}>
                                {{ $site->site_name }} ({{ $site->customer->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Site Context Display -->
            <div id="siteContext" class="site-context">
                <h6 class="mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--ink-soft);">
                    Site Context
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Customer:</strong> <span id="displayCustomer">{{ $door->customerSite->customer->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Region:</strong> <span id="displayRegion">{{ $door->customerSite->region->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Door Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="door_name" value="{{ old('door_name', $door->door_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                        <option value="active" {{ old('status', $door->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Location Details</label>
                    <textarea class="form-control" rows="3" name="location_details">{{ old('location_details', $door->location_details) }}</textarea>
                </div>
            </div>

            <div class="btn-row">
                <a href="{{ route('doors.index') }}" class="btn btn-ef-outline">Cancel</a>
                <button type="submit" class="btn btn-ef-primary">Update Door</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const siteSelect = document.getElementById('siteSelect');
            const siteContext = document.getElementById('siteContext');
            const displayCustomer = document.getElementById('displayCustomer');
            const displayRegion = document.getElementById('displayRegion');

            // Load site details on page load
            loadSiteDetails();

            function loadSiteDetails() {
                const selectedOption = siteSelect.options[siteSelect.selectedIndex];
                const customerName = selectedOption.dataset.customer;
                const regionName = selectedOption.dataset.region;

                if (siteSelect.value) {
                    // Show site context
                    siteContext.style.display = 'block';
                    displayCustomer.textContent = customerName || '-';
                    displayRegion.textContent = regionName || '-';
                } else {
                    // Hide site context
                    siteContext.style.display = 'none';
                }
            }
        });
    </script>
</x-app-layout>
