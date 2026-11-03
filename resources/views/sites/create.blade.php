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
        <div class="title">Add Site</div>
        <div class="sub">Create a new site for a customer</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sites.store') }}" class="mt-4">
            @csrf

            <!-- Customer Selection Context -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select class="form-select" name="customer_id" id="customerSelect" required onchange="loadCustomerDetails()">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    data-region="{{ $customer->region }}"
                                    {{ $customer && $customer->id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Customer Context Display -->
            <div id="customerContext" class="customer-context" style="display: none;">
                <h6 class="mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--ink-soft);">
                    Customer Context
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Customer:</strong> <span id="displayCustomerName">-</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Region:</strong> <span id="displayRegion">-</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Region <span class="text-danger">*</span></label>
                    <select class="form-select" name="region_id" id="regionSelect" required>
                        <option value="">Select region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="site_name" value="{{ old('site_name') }}" required placeholder="e.g., Bata Shop 1">
                </div>
                <div class="col-12">
                    <label class="form-label">Site Address</label>
                    <textarea class="form-control" rows="3" name="address" placeholder="Enter site address">{{ old('address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input class="form-control" name="contact_person" value="{{ old('contact_person') }}" placeholder="Contact person name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input class="form-control" type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="Phone number">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="btn-row">
                <a href="{{ route('sites.index') }}" class="btn btn-ef-outline">Cancel</a>
                <button type="submit" class="btn btn-ef-primary">Save Site</button>
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

            // Load customer details on page load if customer is pre-selected
            if (customerSelect.value) {
                loadCustomerDetails();
            }

            function loadCustomerDetails() {
                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                const customerName = selectedOption.text;
                const customerRegion = selectedOption.dataset.region;

                if (customerSelect.value) {
                    // Show customer context
                    customerContext.style.display = 'block';
                    displayCustomerName.textContent = customerName;
                    displayRegion.textContent = customerRegion || '-';

                    // Auto-select region if customer has one
                    if (customerRegion) {
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
        });
    </script>
</x-app-layout>
