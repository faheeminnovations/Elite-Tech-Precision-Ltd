<x-app-layout>
<div class="card ef p-3">
  <div class="section-head" style="display:flex;justify-content:space-between;align-items:flex-start;">
    <div>
      <h2>Service Record — {{ $service->job_ref }}</h2>
      <div class="section-sub">{{ $service->customer_name }} — {{ $service->service_type }}</div>
    </div>
    <div style="display:flex;gap:8px;">
      <a href="{{ route('services.edit', $service) }}" class="btn-ef-primary" style="padding:8px 16px;border-radius:8px;border:none;color:#1a1300;font-weight:600;font-size:13px;cursor:pointer;text-decoration:none;"><i class="bi bi-pencil me-1"></i>Edit</a>
      <a href="{{ route('services.index') }}" class="btn-ef-outline" style="padding:8px 14px;border-radius:8px;border:1px solid var(--line);color:var(--steel);font-weight:600;font-size:13px;text-decoration:none;background:#fff;cursor:pointer;">Back</a>
    </div>
  </div>
  <div style="padding-top:14px;">
    <div class="row g-3">
      <div class="col-md-4"><div class="cell-sub">JOB REFERENCE</div><div class="mono cell-primary" style="font-size:13px;margin-top:4px;">{{ $service->job_ref }}</div></div>
      <div class="col-md-4"><div class="cell-sub">ENGINEER</div><div style="margin-top:4px;">{{ $service->engineer_name ?? '—' }}</div></div>
      <div class="col-md-4"><div class="cell-sub">STATUS</div><div style="margin-top:4px;">
        @php
          $statusMap = ['scheduled' => 'tag-upcoming', 'in-progress' => 'tag-pending', 'completed' => 'tag-completed', 'cancelled' => 'tag-declined'];
          $statusLabel = ucfirst(str_replace('-', ' ', $service->status));
        @endphp
        <span class="tag {{ $statusMap[$service->status] ?? 'tag-awaiting' }}"><span>{{ $statusLabel }}</span></span>
      </div></div>
      <div class="col-md-6"><div class="cell-sub">VISIT DATE</div><div style="margin-top:4px;">@if($service->visit_date){{ $service->visit_date->format('d/m/Y') }}@else—@endif</div></div>
      <div class="col-md-6"><div class="cell-sub">SITE / INSTALLATION</div><div style="margin-top:4px;">{{ $service->site_installation ?? '—' }}</div></div>
      <div class="col-12"><div class="cell-sub">SERVICE ADDRESS</div><div style="margin-top:4px;">{{ $service->address ?? '—' }}</div></div>
      <div class="col-12"><div class="cell-sub">JOB DETAILS</div><div style="margin-top:4px;">{{ $service->job_details ?? '—' }}</div></div>
      <div class="col-12"><div class="cell-sub">WORK COMPLETED</div><div style="margin-top:4px;">{{ $service->work_completed ?? '—' }}</div></div>
      <div class="col-12"><div class="cell-sub">JOB NOTES</div><div style="margin-top:4px;">{{ $service->job_notes ?? '—' }}</div></div>
      <div class="col-12"><div class="cell-sub">RECOMMENDATIONS / REMEDIAL WORKS</div><div style="margin-top:4px;">{{ $service->recommendations ?? '—' }}</div></div>
      <div class="col-md-6"><div class="cell-sub">REMEDIAL REQUIRED</div><div style="margin-top:4px;">{{ $service->remedial_required === 'yes' ? 'Yes' : 'No' }}</div></div>
      <div class="col-md-6"><div class="cell-sub">REMEDIAL DETAILS</div><div style="margin-top:4px;">{{ $service->remedial_details ?? '—' }}</div></div>
      <div class="col-md-6"><div class="cell-sub">NEXT PPM DUE</div><div style="margin-top:4px;">@if($service->next_ppm_due){{ $service->next_ppm_due->format('d/m/Y') }}@else—@endif</div></div>
    </div>
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--line);">
      <small class="cell-sub"><i class="bi bi-calendar-event me-1"></i>Created: {{ $service->created_at->format('d/m/Y H:i') }}@if($service->updated_at !== $service->created_at) | Updated: {{ $service->updated_at->format('d/m/Y H:i') }}@endif</small>
    </div>
  </div>
</div>
</x-app-layout>
