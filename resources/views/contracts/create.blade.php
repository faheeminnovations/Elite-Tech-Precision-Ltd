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
    </style>
    <div class="card">
        <div class="title">New Contract</div>
        <div class="sub">Create a contract and set PPM schedule</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contracts.store') }}" class="mt-4" id="contractForm" data-form-type="contract">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Contract Ref</label>
                    <input class="form-control" name="contract_ref" value="{{ old('contract_ref') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <div class="input-group">
                        <select class="form-select" name="customer_name" id="customerName" required>
                            <option value="">Select a customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->name }}" data-area="{{ $customer->region }}" data-email="{{ $customer->email }}" {{ old('customer_name') == $customer->name ? 'selected' : '' }}>
                                    {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('customers.create') }}" class="btn btn-outline-secondary" target="_blank" title="Add new customer">
                            <i class="bi bi-plus-circle"></i> Add
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <x-area-select name="area" :value="old('area')" label="Service Area" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site / Installation</label>
                    <input class="form-control" name="site_name" value="{{ old('site_name') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input class="form-control" type="date" name="start_date" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Expiry Date</label>
                    <input class="form-control" type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">PPM Frequency</label>
                    <select class="form-select" name="frequency">
                        <option value="6 monthly" {{ old('frequency') == '6 monthly' ? 'selected' : '' }}>6 monthly</option>
                        <option value="Quarterly" {{ old('frequency') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="Annual" {{ old('frequency') == 'Annual' ? 'selected' : '' }}>Annual</option>
                        <option value="Custom" {{ old('frequency') == 'Custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last PPM</label>
                    <input class="form-control" type="date" name="last_ppm_date" value="{{ old('last_ppm_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next PPM Due</label>
                    <input class="form-control" type="date" name="next_ppm_due" value="{{ old('next_ppm_due') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contract Value (€)</label>
                    <input class="form-control" type="number" step="0.01" min="0" name="contract_value" value="{{ old('contract_value') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Customer Contact Email</label>
                    <input class="form-control" type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Contract Notes</label>
                    <textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expiring" {{ old('status') == 'expiring' ? 'selected' : '' }}>Expiring</option>
                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
            </div>

            <div class="btn-row">
                <a href="{{ route('contracts.index') }}" class="btn btn-ef-outline">Cancel</a>
                <button type="submit" class="btn btn-ef-primary">Save Contract</button>
            </div>
        </form>
    </div>

    <script>
        const customerMap = @json($customers->mapWithKeys(fn ($c) => [$c->name => ['area' => $c->region, 'email' => $c->email]]));

        // Handle Select2 change event
        $(document).ready(function() {
            $('#customerName').on('select2:select', function(e) {
                const data = customerMap[e.params.data.id];
                if (!data) return;
                const areaSelect = document.querySelector('select[name="area"]');
                if (areaSelect && data.area) areaSelect.value = data.area;
                const emailInput = document.getElementById('customerEmail');
                if (emailInput && data.email) emailInput.value = data.email;
            });

            $('#customerName').on('select2:clear', function(e) {
                const areaSelect = document.querySelector('select[name="area"]');
                if (areaSelect) areaSelect.value = '';
                const emailInput = document.getElementById('customerEmail');
                if (emailInput) emailInput.value = '';
            });
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
