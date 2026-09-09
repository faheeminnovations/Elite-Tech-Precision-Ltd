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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="quickCustomerForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Customer Name <span class="required-indicator">*</span></label>
                                <input type="text" class="form-control" name="name" id="newCustomerName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="newCustomerEmail">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Region</label>
                                <select class="form-select" name="region" id="newCustomerRegion">
                                    <option value="">Select region</option>
                                    @foreach(App\Models\Customer::AREAS as $area)
                                        <option value="{{ $area }}">{{ $area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="newCustomerPhone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" id="newCustomerCategory">
                                    <option value="">Select category</option>
                                    <option value="new">New Customer</option>
                                    <option value="chain">Chain Customer</option>
                                </select>
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
                                    <option value="{{ $customer->name }}" data-area="{{ $customer->region }}" data-email="{{ $customer->email }}" {{ old('customer_name', $service->customer_name) == $customer->name ? 'selected' : '' }}>
                                        {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="Add new customer">
                                <i class="bi bi-plus-circle"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Service Area</label>
                        <select class="form-select" name="area" id="area">
                            <option value="">Select area</option>
                            @foreach(\App\Models\Customer::AREAS as $area)
                                <option value="{{ $area }}" {{ old('area', $service->area) == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
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
                        <label class="form-label">Site / Installation</label>
                        <input type="text" class="form-control" name="site_installation" value="{{ old('site_installation', $service->site_installation) }}" placeholder="e.g. Unit 4, Northgate">
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
        const customerMap = @json($customers->mapWithKeys(fn ($c) => [$c->name => ['area' => $c->region, 'email' => $c->email]]));
        const engineerMap = @json($engineers->mapWithKeys(fn ($e) => [$e->id => ['name' => $e->name, 'email' => $e->email]]));
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.wizard-step-content').forEach(el => {
                el.classList.remove('active');
            });
            
            // Show current step
            document.querySelector(`.wizard-step-content[data-step="${step}"]`).classList.add('active');
            
            // Update step indicators
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
            // Validate current step before proceeding
            if (current === 1) {
                const customerName = document.querySelector('[name="customer_name"]').value;
                if (!customerName) {
                    alert('Please select a customer name to proceed.');
                    return;
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

        function addNewCustomer() {
            const form = document.getElementById('quickCustomerForm');
            const formData = new FormData(form);
            
            // Add CSRF token
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
                    // Add new customer to the select dropdown
                    const select = document.getElementById('customer_name');
                    const option = document.createElement('option');
                    option.value = data.customer.name;
                    option.dataset.area = data.customer.region;
                    option.dataset.email = data.customer.email;
                    option.text = data.customer.name + (data.customer.email ? ' (' + data.customer.email + ')' : '');
                    option.selected = true;
                    select.appendChild(option);
                    
                    // Update customer map
                    customerMap[data.customer.name] = {
                        area: data.customer.region,
                        email: data.customer.email
                    };
                    
                    // Auto-fill the form fields
                    const areaSelect = document.querySelector('select[name="area"]');
                    if (areaSelect && data.customer.region) areaSelect.value = data.customer.region;
                    
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                    modal.hide();
                    form.reset();
                    
                    // Show success message with SweetAlert
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
            
            // Add CSRF token
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
                    // Add new engineer to the select dropdown
                    const select = document.getElementById('engineer_id');
                    const option = document.createElement('option');
                    option.value = data.engineer.id;
                    option.dataset.name = data.engineer.name;
                    option.text = data.engineer.name + (data.engineer.email ? ' (' + data.engineer.email + ')' : '');
                    option.selected = true;
                    select.appendChild(option);
                    
                    // Update engineer map
                    engineerMap[data.engineer.id] = {
                        name: data.engineer.name,
                        email: data.engineer.email
                    };
                    
                    // Auto-fill the engineer name field
                    const engineerNameInput = document.getElementById('engineer_name');
                    if (engineerNameInput) engineerNameInput.value = data.engineer.name;
                    
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addEngineerModal'));
                    modal.hide();
                    form.reset();
                    
                    // Show success message with SweetAlert
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

        // Handle engineer selection
        document.getElementById('engineer_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const engineerName = selectedOption.getAttribute('data-name') || selectedOption.text;
            document.getElementById('engineer_name').value = engineerName;
        });

        // Handle customer selection for auto-fill
        document.getElementById('customer_name')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const area = selectedOption.getAttribute('data-area');
            if (area) {
                const areaSelect = document.getElementById('area');
                if (areaSelect) areaSelect.value = area;
            }
        });

        // Initialize Select2 for customer dropdown
        $(document).ready(function() {
            $('#customer_name').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a customer'
            });
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
