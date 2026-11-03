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
        <div class="title">Edit Service Job</div>
        <div class="sub">Update service record for <span class="mono">{{ $service->job_ref }}</span></div>

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

        <!-- Engineer Add Modal -->
        <div class="modal fade" id="addEngineerModal" tabindex="-1" aria-labelledby="addEngineerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEngineerModalLabel">Add New Engineer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="quickEngineerForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Engineer Name <span class="required-indicator">*</span></label>
                                <input type="text" class="form-control" name="name" id="newEngineerName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="required-indicator">*</span></label>
                                <input type="email" class="form-control" name="email" id="newEngineerEmail" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="required-indicator">*</span></label>
                                <input type="password" class="form-control" name="password" id="newEngineerPassword" required minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role <span class="required-indicator">*</span></label>
                                <select class="form-select" name="role" id="newEngineerRole" required>
                                    <option value="">Select role</option>
                                    <option value="engineer">Engineer</option>
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="required-indicator">*</span></label>
                                <select class="form-select" name="status" id="newEngineerStatus" required>
                                    <option value="">Select status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="addNewEngineer()">Add Engineer</button>
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
                <div class="step-label">Service</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Assignment</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-label">Work Details</div>
            </div>
        </div>

        <form action="{{ route('services.update', $service) }}" method="POST" data-form-type="service">
            @csrf
            @method('PUT')
            
            <!-- Step 1: Customer Form -->
            <div class="wizard-step-content active" data-step="1">
                <div class="step-title">Step 1: Customer Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name <span class="required-indicator">*</span></label>
                        <div class="input-group">
                            <select class="form-select" name="customer_name" id="customer_name" required>
                                <option value="">Select a customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->name }}" data-area="{{ $customer->region }}" data-email="{{ $customer->email }}" data-sites="{{ json_encode($customer->sites ?? []) }}" {{ old('customer_name', $service->customer_name) == $customer->name ? 'selected' : '' }}>
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
                                <option value="{{ $region->id }}" {{ old('region_id', $service->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Site <span class="text-muted" style="font-weight:400;">(Select site for this service)</span></label>
                        <select class="form-select" name="site_id" id="siteSelect" onchange="loadDoors()">
                            <option value="">Select a customer first</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Door / Location <span class="text-muted" style="font-weight:400;">(Optional - Select door for this service)</span></label>
                        <select class="form-select" name="door_id" id="doorSelect">
                            <option value="">Select a site first</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Service Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Full service location address">{{ old('address', $service->address) }}</textarea>
                    </div>
                </div>
                
                <div class="btn-row">
                    <a href="{{ route('services.index') }}" class="btn btn-ef-outline">Cancel</a>
                    <button type="button" class="btn btn-ef-primary" onclick="nextStep(1)">Next</button>
                </div>
            </div>

            <!-- Step 2: Service Details -->
            <div class="wizard-step-content" data-step="2">
                <div class="step-title">Step 2: Service Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Job Reference</label>
                        <input type="text" class="form-control" name="job_ref" value="{{ old('job_ref', $service->job_ref) }}" placeholder="e.g. SV-1001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Service Type</label>
                        <select class="form-select" name="service_type">
                            <option value="">Select service type</option>
                            <option value="PPM" {{ old('service_type', $service->service_type) === 'PPM' ? 'selected' : '' }}>PPM</option>
                            <option value="Repair" {{ old('service_type', $service->service_type) === 'Repair' ? 'selected' : '' }}>Repair</option>
                            <option value="Installation" {{ old('service_type', $service->service_type) === 'Installation' ? 'selected' : '' }}>Installation</option>
                            <option value="Service Call" {{ old('service_type', $service->service_type) === 'Service Call' ? 'selected' : '' }}>Service Call</option>
                            <option value="Inspection" {{ old('service_type', $service->service_type) === 'Inspection' ? 'selected' : '' }}>Inspection</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Visit Date</label>
                        <input type="date" class="form-control" name="visit_date" value="{{ old('visit_date', $service->visit_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Select status</option>
                            <option value="scheduled" {{ old('status', $service->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in-progress" {{ old('status', $service->status) === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $service->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $service->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Next PPM Due</label>
                        <input type="date" class="form-control" name="next_ppm_due" value="{{ old('next_ppm_due', $service->next_ppm_due?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mailing Frequency</label>
                        <select class="form-select" name="mailing_frequency">
                            <option value="">No email notifications</option>
                            @foreach(\App\Models\Service::MAILING_FREQUENCIES as $value => $label)
                                <option value="{{ $value }}" {{ old('mailing_frequency', $service->mailing_frequency) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size:11px;">How often to send email notifications for this service</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Next Email Due</label>
                        <input type="date" class="form-control" name="next_email_due" value="{{ old('next_email_due', $service->next_email_due?->format('Y-m-d')) }}">
                        <small class="text-muted" style="font-size:11px;">Optional: Set first email date (will auto-calculate based on frequency if not set)</small>
                    </div>
                </div>
                
                <div class="btn-row">
                    <button type="button" class="btn btn-ef-secondary" onclick="prevStep(2)">Previous</button>
                    <button type="button" class="btn btn-ef-primary" onclick="nextStep(2)">Next</button>
                </div>
            </div>

            <!-- Step 3: Assignment -->
            <div class="wizard-step-content" data-step="3">
                <div class="step-title">Step 3: Engineer Assignment</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Assign Engineer</label>
                        <div class="input-group">
                            <select class="form-select" name="engineer_id" id="engineer_id">
                                <option value="">Select engineer</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}" data-name="{{ $engineer->name }}" {{ old('engineer_id', $service->engineer_id) == $engineer->id ? 'selected' : '' }}>
                                        {{ $engineer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addEngineerModal" title="Add new engineer">
                                <i class="bi bi-plus-circle"></i> Add
                            </button>
                        </div>
                        <input type="hidden" id="engineer_name" name="engineer_name" value="{{ old('engineer_name', $service->engineer_name) }}">
                    </div>
                </div>
                
                <div class="btn-row">
                    <button type="button" class="btn btn-ef-secondary" onclick="prevStep(3)">Previous</button>
                    <button type="button" class="btn btn-ef-primary" onclick="nextStep(3)">Next</button>
                </div>
            </div>

            <!-- Step 4: Work Details -->
            <div class="wizard-step-content" data-step="4">
                <div class="step-title">Step 4: Work Details</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Job Details</label>
                        <textarea class="form-control" name="job_details" rows="3" placeholder="Description of work to be performed">{{ old('job_details', $service->job_details) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Work Completed</label>
                        <textarea class="form-control" name="work_completed" rows="3" placeholder="Summary of work that was actually completed">{{ old('work_completed', $service->work_completed) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Job Notes</label>
                        <textarea class="form-control" name="job_notes" rows="2" placeholder="Engineer notes and observations">{{ old('job_notes', $service->job_notes) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Recommendations / Remedial Works</label>
                        <textarea class="form-control" name="recommendations" rows="3" placeholder="Any recommendations for follow-up work or maintenance">{{ old('recommendations', $service->recommendations) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remedial Works Required</label>
                        <select class="form-select" name="remedial_required">
                            <option value="">Select</option>
                            <option value="no" {{ old('remedial_required', $service->remedial_required) === 'no' ? 'selected' : '' }}>No</option>
                            <option value="yes" {{ old('remedial_required', $service->remedial_required) === 'yes' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remedial Work Details</label>
                        <textarea class="form-control" name="remedial_details" rows="2" placeholder="Details of remedial works required">{{ old('remedial_details', $service->remedial_details) }}</textarea>
                    </div>
                </div>
                
                <div class="btn-row">
                    <button type="button" class="btn btn-ef-secondary" onclick="prevStep(4)">Previous</button>
                    <button type="submit" class="btn btn-ef-primary">Update Service</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const customerMap = @json($customers->mapWithKeys(fn ($c) => [$c->id => ['n' => $c->name, 'e' => $c->email, 'r' => $c->region]]));
        const customerSites = @json($customers->mapWithKeys(fn ($c) => [$c->id => $c->customerSites->map(fn ($s) => ['id' => $s->id, 'name' => $s->site_name, 'region_id' => $s->region_id, 'doors' => $s->doors ? $s->doors->map(fn ($d) => ['id' => $d->id, 'name' => $d->door_name]) : []])]));
        const engineerMap = @json($engineers->mapWithKeys(fn ($e) => [$e->id => ['name' => $e->name, 'email' => $e->email]]));
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            document.querySelectorAll('.wizard-step-content').forEach(el => {
                el.classList.remove('active');
            });

            document.querySelector(`.wizard-step-content[data-step="${step}"]`).classList.add('active');

            document.querySelectorAll('.wizard-steps .step').forEach(el => {
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
                const customerName = document.querySelector('[name="customer_name"]').value;
                const errorDiv = document.getElementById('customer_name_error');
                if (!customerName) {
                    errorDiv.style.display = 'block';
                    return;
                } else {
                    errorDiv.style.display = 'none';
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

                    const select = document.getElementById('customer_name');
                    const option = document.createElement('option');
                    option.value = data.customer.name;
                    option.dataset.email = data.customer.email;
                    option.text = data.customer.name + (data.customer.email ? ' (' + data.customer.email + ')' : '');
                    option.selected = true;
                    select.appendChild(option);

                    $('#customer_name').trigger('change');

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

        function addNewEngineer() {
            const form = document.getElementById('quickEngineerForm');
            const formData = new FormData(form);

            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("users.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('engineer_id');
                    const option = document.createElement('option');
                    option.value = data.engineer.id;
                    option.dataset.name = data.engineer.name;
                    option.text = data.engineer.name + (data.engineer.email ? ' (' + data.engineer.email + ')' : '');
                    option.selected = true;
                    select.appendChild(option);

                    engineerMap[data.engineer.id] = {
                        name: data.engineer.name,
                        email: data.engineer.email
                    };

                    const engineerNameInput = document.getElementById('engineer_name');
                    if (engineerNameInput) engineerNameInput.value = data.engineer.name;

                    const modal = bootstrap.Modal.getInstance(document.getElementById('addEngineerModal'));
                    modal.hide();
                    form.reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Engineer Added',
                        text: 'Engineer has been added successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Unknown error occurred while adding engineer'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error adding engineer. Please try again.'
                });
            });
        }

        function updateSitesDropdown(customerName, regionId = null) {
            const customer = Object.values(customerMap).find(c => c.n === customerName);
            if (!customer) return;

            const customerId = Object.keys(customerMap).find(key => customerMap[key].n === customerName);
            const sites = customerSites[customerId] || [];

            const siteSelect = document.getElementById('siteSelect');
            const doorSelect = document.getElementById('doorSelect');
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
            const siteSelect = document.getElementById('siteSelect');
            const doorSelect = document.getElementById('doorSelect');
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

        document.getElementById('engineer_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const engineerName = selectedOption.getAttribute('data-name') || selectedOption.text;
            document.getElementById('engineer_name').value = engineerName;
        });

        $(document).ready(function() {
            // Initialize with current service values
            const currentCustomerName = '{{ $service->customer_name }}';
            const currentRegionId = '{{ $service->region_id }}';
            const currentSiteId = '{{ $service->site_id }}';
            const currentDoorId = '{{ $service->door_id }}';

            if (currentCustomerName) {
                updateSitesDropdown(currentCustomerName, currentRegionId);
                if (currentSiteId) {
                    setTimeout(() => {
                        document.getElementById('siteSelect').value = currentSiteId;
                        loadDoors();
                        if (currentDoorId) {
                            setTimeout(() => {
                                document.getElementById('doorSelect').value = currentDoorId;
                            }, 100);
                        }
                    }, 100);
                }
            }

            $('#customer_name').on('select2:select', function(e) {
                const customerName = e.params.data.id;
                const customerData = Object.values(customerMap).find(c => c.n === customerName);

                // Auto-select customer's region
                if (customerData && customerData.r) {
                    const regionSelect = document.getElementById('regionSelect');
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

            $('#customer_name').on('select2:clear', function(e) {
                const regionSelect = document.getElementById('regionSelect');
                const siteSelect = document.getElementById('siteSelect');
                const doorSelect = document.getElementById('doorSelect');
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
                const customerName = $('#customer_name').val();
                const regionId = $(this).val();
                if (customerName) {
                    updateSitesDropdown(customerName, regionId);
                }
            });

            $('#customer_name').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a customer'
            });
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
