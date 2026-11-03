<x-app-layout>
    <style>
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
        .btn-ef-primary { background:var(--orange); border:none; color:#1a1300; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; }
        .btn-ef-outline { background:#fff; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .btn-ef-secondary { background:#f8f9fa; border:1px solid var(--line); color:var(--steel); font-weight:600; font-size:13px; padding:10px 16px; border-radius:8px; }
        .form-label { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--ink-soft); }
        .form-control, .form-select { border-radius:8px; border:1px solid var(--line); font-size:13px; padding:9px 11px; }
        .title { font-family:Barlow Condensed,sans-serif; font-size:28px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .sub { font-size:13px; color:var(--ink-soft); }
        .btn-row { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
        
        /* Wizard Styles */
        .wizard-steps { display:flex; justify-content:space-between; margin-bottom:30px; position:relative; }
        .wizard-steps::before { content:''; position:absolute; top:15px; left:0; right:0; height:2px; background:var(--line); z-index:0; }
        .step { display:flex; flex-direction:column; align-items:center; position:relative; z-index:1; flex:1; }
        .step-number { width:32px; height:32px; border-radius:50%; background:#fff; border:2px solid var(--line); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:14px; color:var(--steel); margin-bottom:8px; transition:all 0.3s; }
        .step.active .step-number { background:var(--orange); border-color:var(--orange); color:#1a1300; }
        .step.completed .step-number { background:#28a745; border-color:#28a745; color:#fff; }
        .step-label { font-size:12px; font-weight:600; color:var(--ink-soft); text-transform:uppercase; letter-spacing:0.5px; }
        .step.active .step-label { color:var(--navy); }
        
        .wizard-step-content { display:none; }
        .wizard-step-content.active { display:block; animation:fadeIn 0.3s; }
        
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        
        .step-title { font-size:18px; font-weight:600; color:var(--navy); margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid var(--line); }
        .required-indicator { color:#dc3545; }
    </style>
    <div class="card">
        <div class="title">Edit Contract</div>
        <div class="sub">Update contract details and PPM schedule</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Customer Add Modal -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="quickCustomerForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Customer Name <span class="required-indicator">*</span></label>
                                    <input type="text" class="form-control" name="name" id="newCustomerName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address <span class="required-indicator">*</span></label>
                                    <input type="email" class="form-control" name="email" id="newCustomerEmail" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Sites</label>
                                <div id="sitesContainer">
                                    <div class="row mb-2 site-entry">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control site-name" name="sites[0][name]" placeholder="Site name" required>
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-select site-region" name="sites[0][region_id]" required>
                                                <option value="">Select region</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSiteEntry(this)" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addSiteEntry()">
                                    <i class="bi bi-plus-circle"></i> Add Site
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="addNewCustomer()">Add Customer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wizard Steps Indicator -->
        <div class="wizard-steps mt-4">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Customer</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Contract</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Service</div>
            </div>
        </div>

        <form method="POST" action="{{ route('contracts.update', $contract) }}" class="mt-4" data-form-type="contract">
            @csrf
            @method('PUT')
            
            <!-- Step 1: Customer Form -->
            <div class="wizard-step-content active" data-step="1">
                <div class="step-title">Step 1: Customer Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name <span class="required-indicator">*</span></label>
                        <div class="input-group">
                            <select class="form-select" name="customer_name" id="customerName" required>
                                <option value="">Select a customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->name }}" data-region="{{ $customer->region }}" data-sites="{{ json_encode($customer->sites ?? []) }}" data-email="{{ $customer->email }}" {{ old('customer_name', $contract->customer_name) == $customer->name ? 'selected' : '' }}>
                                        {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="Add new customer">
                                <i class="bi bi-plus-circle"></i> Add
                            </button>
                        </div>
                        <div id="customer_name_error" class="text-danger mt-1" style="display: none; font-size: 12px;">Please select a customer name to proceed.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select class="form-select" name="region_id" id="regionSelect">
                            <option value="">Select region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $contract->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Site <span class="text-muted" style="font-weight:400;">(Select site for this contract)</span></label>
                        <select class="form-select" name="site_id" id="siteSelect" onchange="loadDoors()">
                            <option value="">Select a customer first</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Door / Location <span class="text-muted" style="font-weight:400;">(Optional - Select door for this contract)</span></label>
                        <select class="form-select" name="door_id" id="doorSelect">
                            <option value="">Select a site first</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Customer Contact Email</label>
                        <input class="form-control" type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email', $contract->customer_email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Customer Contact</label>
                        <input class="form-control" type="text" name="customer_contact" value="{{ old('customer_contact', $contract->customer_contact) }}">
                    </div>
                </div>
                
                <div class="btn-row">
                    <a href="{{ route('contracts.index') }}" class="btn btn-ef-outline">Cancel</a>
                    <button type="button" class="btn btn-ef-primary" onclick="nextStep(1)">Next</button>
                </div>
            </div>

            <!-- Step 2: Contract Form -->
            <div class="wizard-step-content" data-step="2">
                <div class="step-title">Step 2: Contract Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contract Ref</label>
                        <input class="form-control" name="contract_ref" value="{{ old('contract_ref', $contract->contract_ref) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input class="form-control" type="date" name="start_date" value="{{ old('start_date', optional($contract->start_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expiry Date</label>
                        <input class="form-control" type="date" name="expiry_date" value="{{ old('expiry_date', optional($contract->expiry_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contract Value (€)</label>
                        <input class="form-control" type="number" step="0.01" min="0" name="contract_value" value="{{ old('contract_value', $contract->contract_value) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Select status</option>
                            <option value="upcoming" {{ old('status', $contract->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expiring" {{ old('status', $contract->status) == 'expiring' ? 'selected' : '' }}>Expiring</option>
                            <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="overdue" {{ old('status', $contract->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Contract Notes</label>
                        <textarea class="form-control" rows="3" name="notes">{{ old('notes', $contract->notes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mailing Frequency</label>
                        <select class="form-select" name="mailing_frequency" id="mailingFrequency" onchange="calculateNextEmailDue()">
                            <option value="">No email notifications</option>
                            @foreach(\App\Models\Contract::MAILING_FREQUENCIES as $value => $label)
                                <option value="{{ $value }}" {{ old('mailing_frequency', $contract->mailing_frequency) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size:11px;">How often to send email notifications for this contract</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Next Email Due</label>
                        <input class="form-control" type="date" name="next_email_due" id="nextEmailDue" value="{{ old('next_email_due', optional($contract->next_email_due)->format('Y-m-d')) }}">
                        <small class="text-muted" style="font-size:11px;">Auto-calculated based on frequency (can be manually changed)</small>
                    </div>
                </div>
                
                <div class="btn-row">
                    <button type="button" class="btn btn-ef-secondary" onclick="prevStep(2)">Previous</button>
                    <button type="button" class="btn btn-ef-primary" onclick="nextStep(2)">Next</button>
                </div>
            </div>

            <!-- Step 3: Service Form -->
            <div class="wizard-step-content" data-step="3">
                <div class="step-title">Step 3: Service Details</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">PPM Frequency</label>
                        <select class="form-select" name="frequency">
                            <option value="">Select frequency</option>
                            <option value="6 monthly" {{ old('frequency', $contract->frequency) == '6 monthly' ? 'selected' : '' }}>6 monthly</option>
                            <option value="Quarterly" {{ old('frequency', $contract->frequency) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="Annual" {{ old('frequency', $contract->frequency) == 'Annual' ? 'selected' : '' }}>Annual</option>
                            <option value="Custom" {{ old('frequency', $contract->frequency) == 'Custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last PPM Date</label>
                        <input class="form-control" type="date" name="last_ppm_date" value="{{ old('last_ppm_date', optional($contract->last_ppm_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Next PPM Due</label>
                        <input class="form-control" type="date" name="next_ppm_due" value="{{ old('next_ppm_due', optional($contract->next_ppm_due)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Reference</label>
                        <input class="form-control" type="text" name="job_ref" value="{{ old('job_ref', $contract->job_ref) }}">
                    </div>
                </div>
                
                <div class="btn-row">
                    <button type="button" class="btn btn-ef-secondary" onclick="prevStep(3)">Previous</button>
                    <button type="submit" class="btn btn-ef-primary">Update Contract</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Customer data with site information
        const customerMap = @json($customers->mapWithKeys(fn ($c) => [$c->id => ['n' => $c->name, 'e' => $c->email, 'r' => $c->region]]));
        const customerSites = @json($customerSitesData);
        let currentStep = 1;
        const totalSteps = 3;

        // Cache DOM elements
        const wizardSteps = document.querySelectorAll('.wizard-step-content');
        const stepIndicators = document.querySelectorAll('.wizard-steps .step');
        const customerNameSelect = document.getElementById('customerName');
        const customerNameError = document.getElementById('customer_name_error');
        const regionSelect = document.getElementById('regionSelect');
        const siteSelect = document.getElementById('siteSelect');
        const doorSelect = document.getElementById('doorSelect');
        const customerEmailInput = document.getElementById('customerEmail');

        function showStep(step) {
            wizardSteps.forEach(el => el.classList.remove('active'));
            const targetStep = document.querySelector(`.wizard-step-content[data-step="${step}"]`);
            if (targetStep) targetStep.classList.add('active');

            stepIndicators.forEach(el => {
                const stepNum = parseInt(el.dataset.step);
                el.classList.remove('active', 'completed');
                if (stepNum === step) {
                    el.classList.add('active');
                } else if (stepNum < step) {
                    el.classList.add('completed');
                }
            });

            currentStep = step;
        }

        function nextStep(current) {
            if (current === 1) {
                const customerName = customerNameSelect.value;
                if (!customerName) {
                    customerNameError.style.display = 'block';
                    return;
                } else {
                    customerNameError.style.display = 'none';
                }
            }

            if (current < totalSteps) {
                showStep(current + 1);
            }
        }

        function prevStep(current) {
            if (current > 1) {
                showStep(current - 1);
            }
        }

        let siteEntryCount = 1;

        function addSiteEntry() {
            const container = document.getElementById('sitesContainer');
            const newEntry = document.createElement('div');
            newEntry.className = 'row mb-2 site-entry';
            newEntry.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control site-name" name="sites[${siteEntryCount}][name]" placeholder="Site name" required>
                </div>
                <div class="col-md-5">
                    <select class="form-select site-region" name="sites[${siteEntryCount}][region_id]" required>
                        <option value="">Select region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSiteEntry(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newEntry);
            siteEntryCount++;

            const entries = container.querySelectorAll('.site-entry');
            if (entries.length > 1) {
                entries[0].querySelector('.btn-danger').style.display = 'block';
            }
        }

        function removeSiteEntry(button) {
            const container = document.getElementById('sitesContainer');
            const entries = container.querySelectorAll('.site-entry');
            
            if (entries.length > 1) {
                button.closest('.site-entry').remove();
                
                const remainingEntries = container.querySelectorAll('.site-entry');
                if (remainingEntries.length === 1) {
                    remainingEntries[0].querySelector('.btn-danger').style.display = 'none';
                }
            }
        }

        function addNewCustomer() {
            const form = document.getElementById('quickCustomerForm');
            const formData = new FormData(form);

            const siteNames = form.querySelectorAll('.site-name');
            let hasSite = false;
            siteNames.forEach(input => {
                if (input.value.trim()) hasSite = true;
            });

            if (!hasSite) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Site Required',
                    text: 'Please add at least one site for the customer.'
                });
                return;
            }

            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("customers.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    customerMap[data.customer.id] = {
                        n: data.customer.name,
                        e: data.customer.email
                    };

                    customerSites[data.customer.id] = data.customer.sites.map(s => ({
                        id: s.id,
                        name: s.site_name,
                        region_id: s.region_id
                    }));

                    const option = document.createElement('option');
                    option.value = data.customer.name;
                    option.dataset.email = data.customer.email;
                    option.text = data.customer.name + (data.customer.email ? ' (' + data.customer.email + ')' : '');
                    option.selected = true;
                    customerNameSelect.appendChild(option);

                    $('#customerName').trigger('change');

                    if (customerEmailInput && data.customer.email) customerEmailInput.value = data.customer.email;

                    form.reset();
                    const container = document.getElementById('sitesContainer');
                    container.innerHTML = `
                        <div class="row mb-2 site-entry">
                            <div class="col-md-5">
                                <input type="text" class="form-control site-name" name="sites[0][name]" placeholder="Site name" required>
                            </div>
                            <div class="col-md-5">
                                <select class="form-select site-region" name="sites[0][region_id]" required>
                                    <option value="">Select region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSiteEntry(this)" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    siteEntryCount = 1;

                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                    modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Added',
                        text: 'Customer has been added successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Unknown error occurred while adding customer'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error adding customer. Please try again.'
                });
            });
        }

        function updateSitesDropdown(customerName, regionId = null) {
            const customer = Object.values(customerMap).find(c => c.n === customerName);
            if (!customer) return;

            const customerId = Object.keys(customerMap).find(key => customerMap[key].n === customerName);
            const sites = customerSites[customerId] || [];

            siteSelect.innerHTML = '<option value="">Select site</option>';
            doorSelect.innerHTML = '<option value="">Select a site first</option>';

            if (sites.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.text = 'No sites available for this customer. Add a site first.';
                option.disabled = true;
                siteSelect.appendChild(option);
                return;
            }

            if (regionId) {
                const filteredSites = sites.filter(s => s.region_id == regionId);
                if (filteredSites.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.text = 'No sites available for this region';
                    option.disabled = true;
                    siteSelect.appendChild(option);
                } else {
                    filteredSites.forEach(site => {
                        const option = document.createElement('option');
                        option.value = site.id;
                        option.text = site.name;
                        siteSelect.appendChild(option);
                    });
                }
            } else {
                sites.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.text = site.name;
                    siteSelect.appendChild(option);
                });
            }
        }

        function loadDoors() {
            const siteId = siteSelect.value;
            doorSelect.innerHTML = '<option value="">Select door (optional)</option>';

            if (!siteId) {
                return;
            }

            // Find the site and its doors
            let siteDoors = [];
            Object.values(customerSites).forEach(sites => {
                const site = sites.find(s => s.id == siteId);
                if (site && site.doors) {
                    siteDoors = site.doors;
                }
            });

            if (siteDoors.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.text = 'No doors available for this site';
                option.disabled = true;
                doorSelect.appendChild(option);
            } else {
                siteDoors.forEach(door => {
                    const option = document.createElement('option');
                    option.value = door.id;
                    option.text = door.name;
                    doorSelect.appendChild(option);
                });
            }
        }

        function calculateNextEmailDue() {
            const frequency = document.getElementById('mailingFrequency').value;
            const nextEmailDueInput = document.getElementById('nextEmailDue');
            
            if (!frequency) {
                nextEmailDueInput.value = '';
                return;
            }

            const today = new Date();
            let nextDate = new Date(today);

            switch(frequency) {
                case 'daily':
                    nextDate.setDate(today.getDate() + 1);
                    break;
                case 'weekly':
                    nextDate.setDate(today.getDate() + 7);
                    break;
                case 'monthly':
                    nextDate.setMonth(today.getMonth() + 1);
                    break;
                case 'quarterly':
                    nextDate.setMonth(today.getMonth() + 3);
                    break;
                case 'yearly':
                    nextDate.setFullYear(today.getFullYear() + 1);
                    break;
                default:
                    nextDate = null;
            }

            if (nextDate) {
                const yyyy = nextDate.getFullYear();
                const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
                const dd = String(nextDate.getDate()).padStart(2, '0');
                nextEmailDueInput.value = `${yyyy}-${mm}-${dd}`;
            }
        }

        $(document).ready(function() {
            // Initialize with current contract values
            const currentCustomerName = '{{ $contract->customer_name }}';
            const currentRegionId = '{{ $contract->region_id }}';
            const currentSiteId = '{{ $contract->site_id }}';
            const currentDoorId = '{{ $contract->door_id }}';

            if (currentCustomerName) {
                updateSitesDropdown(currentCustomerName, currentRegionId);
                if (currentSiteId) {
                    setTimeout(() => {
                        siteSelect.value = currentSiteId;
                        loadDoors();
                        if (currentDoorId) {
                            setTimeout(() => {
                                doorSelect.value = currentDoorId;
                            }, 100);
                        }
                    }, 100);
                }
            }

            $('#customerName').on('select2:select', function(e) {
                const customerName = e.params.data.id;
                const customerData = Object.values(customerMap).find(c => c.n === customerName);

                if (customerData && customerEmailInput && customerData.e) {
                    customerEmailInput.value = customerData.e;
                }

                // Auto-select customer's region
                if (customerData && customerData.r) {
                    for (let i = 0; i < regionSelect.options.length; i++) {
                        if (regionSelect.options[i].text === customerData.r) {
                            regionSelect.selectedIndex = i;
                            // Trigger change to load sites for this region
                            $(regionSelect).trigger('change');
                            break;
                        }
                    }
                } else {
                    updateSitesDropdown(customerName);
                }
            });

            $('#customerName').on('select2:clear', function(e) {
                if (customerEmailInput) customerEmailInput.value = '';
                if (regionSelect) regionSelect.value = '';
                if (siteSelect) {
                    siteSelect.innerHTML = '<option value="">Select a customer first</option>';
                    siteSelect.selectedIndex = 0;
                }
                if (doorSelect) {
                    doorSelect.innerHTML = '<option value="">Select a site first</option>';
                    doorSelect.selectedIndex = 0;
                }
            });

            $('#regionSelect').on('change', function() {
                const customerName = customerNameSelect.value;
                const regionId = $(this).val();
                if (customerName) {
                    updateSitesDropdown(customerName, regionId);
                }
            });

            $('#customerName').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a customer',
                minimumResultsForSearch: 10
            });
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
