<x-app-layout>
<div class="card ef p-3">
  <div class="section-head" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
      <h2>Service Management</h2>
      <div class="section-sub">Create, update and track service jobs, engineer visits, completion records and notes</div>
    </div>
    <a href="{{ route('services.create') }}" class="btn-ef-primary" style="padding:8px 16px;border-radius:8px;border:none;color:#1a1300;font-weight:600;font-size:13px;cursor:pointer;text-decoration:none;"><i class="bi bi-plus-lg"></i> New Service</a>
  </div>

  <x-filter-form
      :action="route('services.index')"
      :keys="['search', 'status', 'engineer', 'service_type', 'area', 'date_from', 'date_to']"
      :total="$services->total()"
      label="service"
      class="mt-3"
      export-route="export.services"
  >
    <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" style="width:220px;" placeholder="Search job ref, customer or site…">
    <select name="status" class="form-select" style="width:160px;">
      <option value="">All statuses</option>
      @foreach($statuses as $value => $label)
        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
      @endforeach
    </select>
    <select name="engineer" class="form-select" style="width:160px;">
      <option value="">All engineers</option>
      @foreach($engineers as $engineer)
        <option value="{{ $engineer->name }}" @selected(($filters['engineer'] ?? '') === $engineer->name)>{{ $engineer->name }}</option>
      @endforeach
    </select>
    <select name="service_type" class="form-select" style="width:160px;">
      <option value="">All types</option>
      @foreach($serviceTypes as $value => $label)
        <option value="{{ $value }}" @selected(($filters['service_type'] ?? '') === $value)>{{ $label }}</option>
      @endforeach
    </select>
    <select name="area" class="form-select" style="width:160px;">
      <option value="">All areas</option>
      @foreach(\App\Models\Customer::AREAS as $area)
        <option value="{{ $area }}" @selected(($filters['area'] ?? '') === $area)>{{ $area }}</option>
      @endforeach
    </select>
    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control" style="width:150px;" title="Visit date from">
    <span class="cell-sub">to</span>
    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control" style="width:150px;" title="Visit date to">
  </x-filter-form>

  <div class="table-responsive mt-3">
    <table class="ef-table">
      <thead><tr><th>Job Ref</th><th>Customer</th><th>Area</th><th>Service Type</th><th>Engineer</th><th>Visit Date</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($services as $service)
          <tr>
            <td class="mono cell-primary">{{ $service->job_ref }}</td>
            <td class="cell-primary">{{ $service->customer_name }}</td>
            <td class="cell-sub">{{ $service->area ?? '—' }}</td>
            <td>{{ $service->service_type }}</td>
            <td>{{ $service->engineer?->name ?? $service->engineer_name ?? '—' }}</td>
            <td class="mono">@if($service->visit_date){{ $service->visit_date->format('d/m/Y') }}@else—@endif</td>
            <td>
              @php
                $statusMap = [
                  'scheduled' => 'tag-upcoming',
                  'in-progress' => 'tag-pending',
                  'completed' => 'tag-completed',
                  'cancelled' => 'tag-declined'
                ];
                $statusLabel = ucfirst(str_replace('-', ' ', $service->status));
              @endphp
              <span class="tag {{ $statusMap[$service->status] ?? 'tag-awaiting' }}"><span>{{ $statusLabel }}</span></span>
            </td>
            <td class="cell-sub">{{ Str::limit($service->job_notes, 40) ?? '—' }}</td>
            <td><div class="crud-actions">
              <a href="{{ route('services.show', $service) }}" class="btn btn-view" title="View" style="background:#E4EBF6;color:var(--steel);border:0;width:32px;height:32px;padding:0;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-eye"></i></a>
              <a href="{{ route('services.edit', $service) }}" class="btn btn-edit" title="Edit" style="background:#E4EBF6;color:var(--steel);border:0;width:32px;height:32px;padding:0;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-pencil"></i></a>
              <button class="btn btn-delete" onclick="deleteService('{{ route('services.destroy', $service) }}', '{{ $service->job_ref }}')" title="Delete" style="background:#FCE7E4;color:#C0392B;border:0;width:32px;height:32px;padding:0;border-radius:7px;"><i class="bi bi-trash"></i></button>
            </div></td>
          </tr>
        @empty
          <tr><td colspan="9" style="text-align:center;padding:40px 20px;color:var(--ink-soft);">
            <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;color:var(--line);"></i>
            @if(request()->hasAny(['search', 'status', 'engineer', 'service_type', 'area', 'date_from', 'date_to']))
              No service records match your filters. <a href="{{ route('services.index') }}">Clear filters</a>
            @else
              No service records found
            @endif
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($services->hasPages())
    <div class="mt-3">{{ $services->links() }}</div>
  @endif
</div>

<script>
// Real-time search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const filterForm = document.querySelector('form');

    if (searchInput && filterForm) {
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                filterForm.submit();
            }, 500); // Wait 500ms after user stops typing
        });
    }

    // Auto-submit on dropdown changes
    const dropdowns = filterForm.querySelectorAll('select');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            filterForm.submit();
        });
    });
});

function deleteService(url, jobRef) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Service?',
            text: 'Are you sure you want to delete service job ' + jobRef + '? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#13315C',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDeleteForm(url);
            }
        });
    } else {
        if (confirm('Delete this service?')) {
            submitDeleteForm(url);
        }
    }
}

function submitDeleteForm(url) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
</x-app-layout>
