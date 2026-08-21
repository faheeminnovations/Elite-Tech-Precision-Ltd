<x-app-layout>
    <div class="card ef p-3 mb-3">
        <div class="section-head">
            <div>
                <h2>Manual Data Entry</h2>
                <div class="section-sub">Customer, service, contract and PPM response entry templates</div>
            </div>
            <span class="tag tag-upcoming"><span>DD/MM/YYYY</span></span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Customer Entry Form --}}
        <div class="col-xl-6">
            <div class="card ef p-3 h-100">
                <div class="section-head">
                    <div>
                        <h2>Customer Entry Form</h2>
                        <div class="section-sub">Manual customer registration</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Name *</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Ref Number *</label>
                            <input class="form-control" name="job_ref" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Details</label>
                            <textarea class="form-control" name="job_details" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Date of Completion</label>
                            <input class="form-control" type="date" name="completion_date">
                        </div>
                        <div class="col-md-6">
                            <x-category-select :value="old('category', 'new')" />
                        </div>
                        <div class="col-md-6">
                            <x-area-select :value="old('region')" />
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Phone</label>
                            <input class="form-control" type="tel" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Email</label>
                            <input class="form-control" type="email" name="email">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Notes</label>
                            <textarea class="form-control" name="job_notes" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Status</label>
                            <select class="form-select" name="status">
                                <option value="new">New</option>
                                <option value="active">Active</option>
                                <option value="nocontract">No Contract</option>
                                <option value="updated">Updated</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn-ef-outline">Clear</button>
                        <button class="btn btn-ef-primary" type="submit"><i class="bi bi-save me-1"></i> Save Customer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Service Details Entry Form --}}
        <div class="col-xl-6">
            <div class="card ef p-3 h-100">
                <div class="section-head">
                    <div>
                        <h2>Service Details Entry Form</h2>
                        <div class="section-sub">Manual service / maintenance visit record</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('services.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Ref Number *</label>
                            <input class="form-control" name="job_ref" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Name *</label>
                            <input class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Service Type</label>
                            <select class="form-select" name="service_type">
                                <option value="PPM">PPM</option>
                                <option value="Repair">Repair</option>
                                <option value="Installation">Installation</option>
                                <option value="Service Call">Service Call</option>
                                <option value="Inspection">Inspection</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Service / Visit Date</label>
                            <input class="form-control" type="date" name="visit_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Engineer Name</label>
                            <input class="form-control" name="engineer_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Site / Installation</label>
                            <input class="form-control" name="site_installation">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Service Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Details</label>
                            <textarea class="form-control" name="job_details" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Work Completed</label>
                            <textarea class="form-control" name="work_completed" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Notes</label>
                            <textarea class="form-control" name="job_notes" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Recommendations</label>
                            <textarea class="form-control" name="recommendations" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Remedial Works Required</label>
                            <select class="form-select" name="remedial_required">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Remedial Work Details</label>
                            <textarea class="form-control" name="remedial_details" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Service Status</label>
                            <select class="form-select" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Next PPM Due Date</label>
                            <input class="form-control" type="date" name="next_ppm_due">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn-ef-outline">Clear</button>
                        <button class="btn btn-ef-primary" type="submit"><i class="bi bi-save me-1"></i> Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        {{-- Contract Entry Form --}}
        <div class="col-xl-6">
            <div class="card ef p-3 h-100">
                <div class="section-head">
                    <div>
                        <h2>Contract Entry</h2>
                        <div class="section-sub">Manual contract and PPM schedule registration</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('contracts.store') }}">
                    @csrf
                    <input type="hidden" name="return_to" value="data-entry">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Available</label>
                            <select class="form-select" name="contract_available">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Ref *</label>
                            <input class="form-control" name="contract_ref" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Name *</label>
                            <input class="form-control" name="customer_name" id="contractCustomerName" list="contractCustomerList" required>
                            <datalist id="contractCustomerList">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->name }}" data-job-ref="{{ $customer->job_ref }}" data-email="{{ $customer->email }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Ref Number</label>
                            <input class="form-control" name="job_ref" id="contractJobRef">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Site / Installation</label>
                            <input class="form-control" name="site_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Start Date</label>
                            <input class="form-control" type="date" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Expiry Date</label>
                            <input class="form-control" type="date" name="expiry_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">PPM Frequency</label>
                            <select class="form-select" name="frequency">
                                @foreach(\App\Models\Contract::FREQUENCIES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Status</label>
                            <select class="form-select" name="status">
                                @foreach(\App\Models\Contract::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected($value === 'active')>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Last PPM Date</label>
                            <input class="form-control" type="date" name="last_ppm_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Next PPM Due Date</label>
                            <input class="form-control" type="date" name="next_ppm_due">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Value</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="contract_value">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Contract Contact</label>
                            <input class="form-control" name="customer_contact">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Email</label>
                            <input class="form-control" type="email" name="customer_email" id="contractCustomerEmail">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn-ef-outline">Clear</button>
                        <button class="btn btn-ef-primary" type="submit"><i class="bi bi-save me-1"></i> Save Contract</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Customer PPM Response Entry Form --}}
        <div class="col-xl-6">
            <div class="card ef p-3 h-100">
                <div class="section-head">
                    <div>
                        <h2>Customer PPM Response Entry</h2>
                        <div class="section-sub">Record customer replies to PPM reminder emails</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('responses.store') }}">
                    @csrf
                    <input type="hidden" name="return_to" value="data-entry">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">PPM Reference</label>
                            <input class="form-control" name="ppm_reference">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Contract Ref</label>
                            <input class="form-control" name="contract_ref">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Job Ref Number</label>
                            <input class="form-control" name="job_ref">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Name *</label>
                            <input class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Email Sent Date</label>
                            <input class="form-control" type="date" name="reminder_sent">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Response</label>
                            <select class="form-select" name="response">
                                @foreach(\App\Models\Response::RESPONSE_TYPES as $value => $label)
                                    <option value="{{ $value }}" @selected($value === 'no_response')>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Response Date</label>
                            <input class="form-control" type="date" name="responded_on">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Response Time</label>
                            <input class="form-control" type="time" name="response_time">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Internal Notification Sent</label>
                            <select class="form-select" name="internal_notification_sent">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Response Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn-ef-outline">Clear</button>
                        <button class="btn btn-ef-primary" type="submit"><i class="bi bi-save me-1"></i> Save Response</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const contractCustomerMap = @json($customers->mapWithKeys(fn ($c) => [$c->name => ['job_ref' => $c->job_ref, 'email' => $c->email]]));

        document.getElementById('contractCustomerName')?.addEventListener('change', function () {
            const data = contractCustomerMap[this.value];
            if (!data) return;
            const jobRefInput = document.getElementById('contractJobRef');
            if (jobRefInput && data.job_ref) jobRefInput.value = data.job_ref;
            const emailInput = document.getElementById('contractCustomerEmail');
            if (emailInput && data.email) emailInput.value = data.email;
        });
    </script>
</x-app-layout>
