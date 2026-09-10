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
                                    <option value="{{ $customer->name }}" data-area="{{ $customer->region }}" data-email="{{ $customer->email }}" {{ old('customer_name', $contract->customer_name) == $customer->name ? 'selected' : '' }}>
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
                        <label class="form-label">Service Area</label>
                        <x-area-select name="area" :value="old('area', $contract->area)" label="" />
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
                    <div class="col-md-6">
                        <label class="form-label">Site / Installation</label>
                        <input class="form-control" name="site_name" value="{{ old('site_name', $contract->site_name) }}">
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
        // Optimize customer data structure with abbreviated keys for smaller payload
        const customerMap = @json($customers->mapWithKeys(fn ($c) => [$c->name => ['a' => $c->region, 'e' => $c->email]]));
        let currentStep = 1;
        const totalSteps = 3;
        
        // Cache DOM elements for better performance
        const wizardSteps = document.querySelectorAll('.wizard-step-content');
        const stepIndicators = document.querySelectorAll('.wizard-steps .step');
        const customerNameSelect = document.getElementById('customerName');
        const customerNameError = document.getElementById('customer_name_error');
        const areaSelect = document.querySelector('select[name="area"]');
        const customerEmailInput = document.getElementById('customerEmail');

        function showStep(step) {
            // Hide all steps at once for better performance
            wizardSteps.forEach(el => el.classList.remove('active'));
            
            // Show current step
            const targetStep = document.querySelector(`.wizard-step-content[data-step="${step}"]`);
            if (targetStep) targetStep.classList.add('active');
            
            // Update step indicators efficiently
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
            // Validate current step before proceeding
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
                    // Add new customer to the select dropdown efficiently
                    const option = document.createElement('option');
                    option.value = data.customer.name;
                    option.dataset.area = data.customer.region;
                    option.dataset.email = data.customer.email;
                    option.text = data.customer.name + (data.customer.email ? ' (' + data.customer.email + ')' : '');
                    option.selected = true;
                    customerNameSelect.appendChild(option);
                    
                    // Update customer map with abbreviated keys
                    customerMap[data.customer.name] = {
                        a: data.customer.region,
                        e: data.customer.email
                    };
                    
                    // Auto-fill the form fields
                    if (areaSelect && data.customer.region) areaSelect.value = data.customer.region;
                    if (customerEmailInput && data.customer.email) customerEmailInput.value = data.customer.email;
                    
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

        // Handle Select2 change event with optimized performance
        $(document).ready(function() {
            $('#customerName').on('select2:select', function(e) {
                const data = customerMap[e.params.data.id];
                if (!data) return;
                if (areaSelect && data.a) areaSelect.value = data.a;
                if (customerEmailInput && data.e) customerEmailInput.value = data.e;
            });

            $('#customerName').on('select2:clear', function(e) {
                if (areaSelect) areaSelect.value = '';
                if (customerEmailInput) customerEmailInput.value = '';
            });

            // Initialize Select2 for customer dropdown with optimized settings
            $('#customerName').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a customer',
                minimumResultsForSearch: 10 // Only show search if more than 10 items
            });
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
