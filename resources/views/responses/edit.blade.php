<x-app-layout>
    <div class="card" style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;">
        <div style="font-family:Barlow Condensed,sans-serif;font-size:28px;font-weight:700;color:var(--navy);margin-bottom:4px;">Edit Response</div>
        <div style="font-size:13px;color:var(--ink-soft);">Update response for {{ $response->customer_name }}</div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('responses.update', $response) }}" class="mt-4">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer Name *</label>
                    <select class="form-select" name="customer_name" id="customerName" required>
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->name }}" {{ old('customer_name', $response->customer_name) == $customer->name ? 'selected' : '' }}>
                                {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Site Name</label>
                    <input class="form-control" name="site_name" value="{{ old('site_name', $response->site_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">PPM Due Date</label>
                    <input class="form-control" type="date" name="ppm_due" value="{{ old('ppm_due', optional($response->ppm_due)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder Sent</label>
                    <input class="form-control" type="date" name="reminder_sent" value="{{ old('reminder_sent', optional($response->reminder_sent)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Response</label>
                    <select class="form-select" name="response">
                        <option value="awaiting" {{ old('response', $response->response) == 'awaiting' ? 'selected' : '' }}>Awaiting</option>
                        <option value="accepted" {{ old('response', $response->response) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ old('response', $response->response) == 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Responded On</label>
                    <input class="form-control" type="date" name="responded_on" value="{{ old('responded_on', optional($response->responded_on)->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Notes</label>
                    <textarea class="form-control" rows="3" name="notes">{{ old('notes', $response->notes) }}</textarea>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <a href="{{ route('responses.index') }}" class="btn btn-ef-outline">Cancel</a>
                <button type="submit" class="btn btn-ef-primary">Update Response</button>
            </div>
        </form>
    </div>
</x-app-layout>
