<x-app-layout>
    <div class="card" style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;">
        <div style="font-family:Barlow Condensed,sans-serif;font-size:28px;font-weight:700;color:var(--navy);margin-bottom:8px;">{{ $response->customer_name }}</div>
        <div style="font-size:13px;color:var(--ink-soft);">Customer response details</div>
        <div class="mt-3">
            @php
                $rmap = ['accepted'=>'tag-accepted','declined'=>'tag-declined','awaiting'=>'tag-awaiting'];
                $rlbl = ['accepted'=>'Accepted','declined'=>'Declined','awaiting'=>'Awaiting'];
            @endphp
            <span class="tag {{ $rmap[$response->response] ?? 'tag-awaiting' }}"><span>{{ $rlbl[$response->response] ?? ucfirst($response->response) }}</span></span>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Customer</div>
                <div style="font-size:15px;margin-top:4px;font-weight:600;color:var(--navy);">{{ $response->customer_name }}</div>
            </div>
            <div class="col-md-6">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Site</div>
                <div style="font-size:15px;margin-top:4px;">{{ $response->site_name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">PPM Due</div>
                <div class="mono" style="font-size:15px;margin-top:4px;">{{ $response->ppm_due ? $response->ppm_due->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Reminder Sent</div>
                <div class="mono" style="font-size:15px;margin-top:4px;">{{ $response->reminder_sent ? $response->reminder_sent->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Responded On</div>
                <div class="mono" style="font-size:15px;margin-top:4px;">{{ $response->responded_on ? $response->responded_on->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="col-12">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--ink-soft);">Notes</div>
                <div style="font-size:15px;margin-top:4px;">{{ $response->notes ?? '—' }}</div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
            <a href="{{ route('responses.edit', $response) }}" class="btn btn-ef-primary">Edit</a>
            <a href="{{ route('responses.index') }}" class="btn btn-ef-outline">Back to list</a>
        </div>
    </div>
</x-app-layout>
