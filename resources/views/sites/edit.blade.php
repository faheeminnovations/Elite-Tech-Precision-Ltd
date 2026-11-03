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
        .customer-context { background:#f8f9fa; border:1px solid var(--line); border-radius:8px; padding:16px; margin-bottom:20px; }
    </style>
    <div class="card">
        <div class="title">Edit Site</div>
        <div class="sub">Update site information</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sites.update', $site) }}" class="mt-4">
            @csrf
            @method('PUT')

            <!-- Customer Selection Context -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select class="form-select" name="customer_id" id="customerSelect" required onchange="loadCustomerDetails()">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    data-region="{{ $customer->region }}"
                                    {{ $site->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Customer Context Display -->
            <div id="customerContext" class="customer-context">
                <h6 class="mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--ink-soft);">
                    Customer Context
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Customer:</strong> <span id="displayCustomerName">{{ $site->customer->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Region:</strong> <span id="displayRegion">{{ $site->customer->region ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Region <span class="text-danger">*</span></label>
                    <select class="form-select" name="region_id" id="regionSelect" required>
                        <option value="">Select region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ $site->region_id == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="site_name" value="{{ old('site_name', $site->site_name) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Site Address</label>
                    <textarea class="form-control" rows="3" name="address">{{ old('address', $site->address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input class="form-control" name="contact_person" value="{{ old('contact_person', $site->contact_person) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input class="form-control" type="tel" name="contact_number" value="{{ old('contact_number', $site->contact_number) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email', $site->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                        <option value="active" {{ old('status', $site->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="btn-row">
                <a href="{{ route('sites.index') }}" class="btn btn-ef-outline">Cancel</a>
                <button type="submit" class="btn btn-ef-primary">Update Site</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customerSelect');
            const regionSelect = document.getElementById('regionSelect');
            const customerContext = document.getElementById('customerContext');
            const displayCustomerName = document.getElementById('displayCustomerName');
            const displayRegion = document.getElementById('displayRegion');

            // Load customer details on page load
            loadCustomerDetails();

            function loadCustomerDetails() {
                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                const customerName = selectedOption.text;
                const customerRegion = selectedOption.dataset.region;

                if (customerSelect.value) {
                    // Show customer context
                    customerContext.style.display = 'block';
                    displayCustomerName.textContent = customerName;
                    displayRegion.textContent = customerRegion || '-';

                    // Auto-select region if customer has one and region hasn't been manually changed
                    if (customerRegion && !regionSelect.dataset.manuallyChanged) {
                        for (let i = 0; i < regionSelect.options.length; i++) {
                            if (regionSelect.options[i].text === customerRegion) {
                                regionSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                } else {
                    // Hide customer context
                    customerContext.style.display = 'none';
                    regionSelect.selectedIndex = 0;
                }
            }

            // Track if region is manually changed
            regionSelect.addEventListener('change', function() {
                regionSelect.dataset.manuallyChanged = 'true';
            });
        });
    </script>
</x-app-layout>
