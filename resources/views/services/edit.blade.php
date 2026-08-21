<x-app-layout>
<div class="card ef p-3">
  <div class="section-head">
    <div>
      <h2>Edit Service Job</h2>
      <div class="section-sub">Update service record for <span class="mono">{{ $service->job_ref }}</span></div>
    </div>
  </div>
  <div class="card-body" style="padding:0;">
  <form action="{{ route('services.update', $service) }}" method="POST" data-form-type="service">
    @csrf
    @method('PUT')
    <div class="row g-3" style="padding-top:16px;">
                        <div class="col-md-6">
                            <label for="job_ref" class="form-label fw-bold">Job Reference <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('job_ref') is-invalid @enderror" id="job_ref" name="job_ref" value="{{ old('job_ref', $service->job_ref) }}" required placeholder="e.g. SV-1001">
                            @error('job_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="customer_name" class="form-label fw-bold">Customer Name <span class="text-danger">*</span></label>
                            <select class="form-select @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" required>
                                <option value="">Select a customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->name }}" data-area="{{ $customer->region }}" data-email="{{ $customer->email }}" {{ old('customer_name', $service->customer_name) == $customer->name ? 'selected' : '' }}>
                                        {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="area" class="form-label fw-bold">Service Area</label>
                            <select class="form-select @error('area') is-invalid @enderror" id="area" name="area">
                                <option value="">Select area</option>
                                @foreach(\App\Models\Customer::AREAS as $area)
                                    <option value="{{ $area }}" {{ old('area', $service->area) == $area ? 'selected' : '' }}>{{ $area }}</option>
                                @endforeach
                            </select>
                            @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="service_type" class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                <option value="">Select service type</option>
                                <option value="PPM" {{ old('service_type', $service->service_type) === 'PPM' ? 'selected' : '' }}>PPM</option>
                                <option value="Repair" {{ old('service_type', $service->service_type) === 'Repair' ? 'selected' : '' }}>Repair</option>
                                <option value="Installation" {{ old('service_type', $service->service_type) === 'Installation' ? 'selected' : '' }}>Installation</option>
                                <option value="Service Call" {{ old('service_type', $service->service_type) === 'Service Call' ? 'selected' : '' }}>Service Call</option>
                                <option value="Inspection" {{ old('service_type', $service->service_type) === 'Inspection' ? 'selected' : '' }}>Inspection</option>
                            </select>
                            @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="engineer_id" class="form-label fw-bold">Assign Engineer</label>
                            <div class="input-group">
                                <select class="form-select @error('engineer_id') is-invalid @enderror" id="engineer_id" name="engineer_id">
                                    <option value="">Select engineer</option>
                                    @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}" data-name="{{ $engineer->name }}" {{ old('engineer_id', $service->engineer_id) == $engineer->id ? 'selected' : '' }}>
                                            {{ $engineer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('users.create') }}" class="btn btn-outline-secondary" target="_blank" title="Add new engineer">
                                    <i class="bi bi-plus-circle"></i> Add
                                </a>
                            </div>
                            <input type="hidden" id="engineer_name" name="engineer_name" value="{{ old('engineer_name', $service->engineer_name) }}">
                            @error('engineer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="visit_date" class="form-label fw-bold">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date', $service->visit_date?->format('Y-m-d')) }}" required>
                            @error('visit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Select status</option>
                                <option value="scheduled" {{ old('status', $service->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in-progress" {{ old('status', $service->status) === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $service->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $service->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="site_installation" class="form-label fw-bold">Site / Installation</label>
                            <input type="text" class="form-control @error('site_installation') is-invalid @enderror" id="site_installation" name="site_installation" value="{{ old('site_installation', $service->site_installation) }}" placeholder="e.g. Unit 4, Northgate">
                            @error('site_installation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="next_ppm_due" class="form-label fw-bold">Next PPM Due</label>
                            <input type="date" class="form-control @error('next_ppm_due') is-invalid @enderror" id="next_ppm_due" name="next_ppm_due" value="{{ old('next_ppm_due', $service->next_ppm_due?->format('Y-m-d')) }}">
                            @error('next_ppm_due')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-bold">Service Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Full service location address">{{ old('address', $service->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="job_details" class="form-label fw-bold">Job Details</label>
                            <textarea class="form-control @error('job_details') is-invalid @enderror" id="job_details" name="job_details" rows="3" placeholder="Description of work to be performed">{{ old('job_details', $service->job_details) }}</textarea>
                            @error('job_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="work_completed" class="form-label fw-bold">Work Completed</label>
                            <textarea class="form-control @error('work_completed') is-invalid @enderror" id="work_completed" name="work_completed" rows="3" placeholder="Summary of work that was actually completed">{{ old('work_completed', $service->work_completed) }}</textarea>
                            @error('work_completed')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="job_notes" class="form-label fw-bold">Job Notes</label>
                            <textarea class="form-control @error('job_notes') is-invalid @enderror" id="job_notes" name="job_notes" rows="2" placeholder="Engineer notes and observations">{{ old('job_notes', $service->job_notes) }}</textarea>
                            @error('job_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="recommendations" class="form-label fw-bold">Recommendations / Remedial Works</label>
                            <textarea class="form-control @error('recommendations') is-invalid @enderror" id="recommendations" name="recommendations" rows="3" placeholder="Any recommendations for follow-up work or maintenance">{{ old('recommendations', $service->recommendations) }}</textarea>
                            @error('recommendations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="remedial_required" class="form-label fw-bold">Remedial Works Required</label>
                            <select class="form-select @error('remedial_required') is-invalid @enderror" id="remedial_required" name="remedial_required">
                                <option value="">Select</option>
                                <option value="no" {{ old('remedial_required', $service->remedial_required) === 'no' ? 'selected' : '' }}>No</option>
                                <option value="yes" {{ old('remedial_required', $service->remedial_required) === 'yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('remedial_required')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="remedial_details" class="form-label fw-bold">Remedial Work Details</label>
                            <textarea class="form-control @error('remedial_details') is-invalid @enderror" id="remedial_details" name="remedial_details" rows="2" placeholder="Details of remedial works required">{{ old('remedial_details', $service->remedial_details) }}</textarea>
                            @error('remedial_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;padding-top:12px;border-top:1px solid var(--line);">
      <button type="submit" class="btn-ef-primary" style="padding:8px 16px;border-radius:8px;border:none;color:#1a1300;font-weight:600;font-size:13px;cursor:pointer;"><i class="bi bi-save me-1"></i>Update Service</button>
      <a href="{{ route('services.index') }}" class="btn-ef-outline" style="padding:8px 14px;border-radius:8px;border:1px solid var(--line);color:var(--steel);font-weight:600;font-size:13px;text-decoration:none;background:#fff;cursor:pointer;">Cancel</a>
    </div>
  </form>
  </div>
</div>

<script>
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
</script>
<script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
