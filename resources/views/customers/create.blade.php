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
        <div class="card">
            <div class="title">Add Customer</div>
            <div class="sub">Create a new customer register entry</div>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customers.store') }}" class="mt-4" data-form-type="customer">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Ref Number</label>
                        <input class="form-control" name="job_ref" value="{{ old('job_ref') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Details</label>
                        <input class="form-control" name="job_details" value="{{ old('job_details') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Completion</label>
                        <input class="form-control" type="date" name="completion_date" value="{{ old('completion_date') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="3" name="address">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Job Notes</label>
                        <textarea class="form-control" rows="3" name="job_notes">{{ old('job_notes') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="nocontract" {{ old('status') == 'nocontract' ? 'selected' : '' }}>No Contract</option>
                            <option value="updated" {{ old('status') == 'updated' ? 'selected' : '' }}>Updated</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <x-category-select :value="old('category', 'new')" />
                    </div>
                    <div class="col-md-6">
                        <x-area-select :value="old('region')" />
                    </div>
                </div>

                <div class="btn-row">
                    <a href="{{ route('customers.index') }}" class="btn btn-ef-outline">Cancel</a>
                    <button type="submit" class="btn btn-ef-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</x-app-layout>
