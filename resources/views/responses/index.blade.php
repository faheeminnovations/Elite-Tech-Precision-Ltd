<x-app-layout>
    <div class="card ef p-3">
        <div class="section-head" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2>Customer Responses</h2>
                <div class="section-sub">Accept / decline replies captured from PPM reminder emails</div>
            </div>
            <a href="{{ route('responses.create') }}" class="btn btn-ef-primary"><i class="bi bi-plus-lg"></i> Add Response</a>
        </div>

        <x-filter-form
            :action="route('responses.index')"
            :keys="['search', 'response', 'date_from', 'date_to']"
            :total="$responses->count()"
            label="response"
            class="mt-2"
            export-route="export.responses"
        >
            <select name="response" class="form-select" style="width:160px;">
                <option value="">All responses</option>
                @foreach($responseTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['response'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" style="width:220px;" placeholder="Search customer or site…">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control" style="width:150px;" title="PPM due from">
            <span class="cell-sub">to</span>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control" style="width:150px;" title="PPM due to">
        </x-filter-form>

        <div class="table-responsive">
            <table class="ef-table">
                <thead><tr><th>Customer / Site</th><th>PPM Due</th><th>Reminder Sent</th><th>Response</th><th>Responded On</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($responses as $response)
                        <tr>
                            <td>
                                <div class="cell-primary">{{ $response->customer_name }}</div>
                                <div class="cell-sub">{{ $response->site_name ?? '—' }}</div>
                            </td>
                            <td class="mono cell-sub">{{ $response->ppm_due ? $response->ppm_due->format('d M Y') : '—' }}</td>
                            <td class="mono cell-sub">{{ $response->reminder_sent ? $response->reminder_sent->format('d M Y') : '—' }}</td>
                            <td>
                                @php
                                    $rmap = ['accepted'=>'tag-accepted','declined'=>'tag-declined','awaiting'=>'tag-awaiting'];
                                    $rlbl = ['accepted'=>'Accepted','declined'=>'Declined','awaiting'=>'Awaiting'];
                                @endphp
                                <span class="tag {{ $rmap[$response->response] ?? 'tag-awaiting' }}"><span>{{ $rlbl[$response->response] ?? ucfirst($response->response) }}</span></span>
                            </td>
                            <td class="mono cell-sub">{{ $response->responded_on ? $response->responded_on->format('d M Y') : '—' }}</td>
                            <td>
                                <div class="crud-actions">
                                    <a class="btn btn-view" href="{{ route('responses.show', $response) }}" title="View"><i class="bi bi-eye"></i></a>
                                    <a class="btn btn-edit" href="{{ route('responses.edit', $response) }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-delete" onclick="deleteResponse('{{ route('responses.destroy', $response) }}', '{{ $response->customer_name }}')" title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-note">
                                <i class="bi bi-envelope-check d-block mb-2"></i>
                                @if(request()->hasAny(['search', 'response', 'date_from', 'date_to']))
                                    No responses match your filters. <a href="{{ route('responses.index') }}">Clear filters</a>
                                @else
                                    No customer responses recorded yet.
                                @endif
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteResponse(url, customerName) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Response?',
                    text: 'Are you sure you want to delete response from ' + customerName + '? This action cannot be undone.',
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
                // Fallback to native confirm if SweetAlert is not loaded
                if (confirm('Delete this response?')) {
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
