<x-app-layout>
    <div class="section-head" style="margin-bottom:24px;">
        <div>
            <h2>User Management</h2>
            <div class="section-sub">Create and manage admin, engineer, and manager login accounts</div>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-ef-primary"><i class="bi bi-person-plus"></i> Add User</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-3" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card ef p-3">
        <div class="table-responsive">
            <table class="ef-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="cell-primary">{{ $user->name }}</td>
                            <td class="mono cell-sub">{{ $user->email }}</td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="tag tag-upcoming"><span>Admin</span></span>
                                @elseif($user->isManager())
                                    <span class="tag tag-in-progress"><span>Manager</span></span>
                                @elseif($user->isEngineer())
                                    <span class="tag tag-accepted"><span>Engineer</span></span>
                                @else
                                    <span class="tag tag-awaiting"><span>No role</span></span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="tag tag-completed"><span>Active</span></span>
                                @else
                                    <span class="tag tag-overdue"><span>Inactive</span></span>
                                @endif
                            </td>
                            <td class="cell-sub">{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Never' }}</td>
                            <td>
                                <div class="crud-actions">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-edit" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-delete" onclick="deleteUser('{{ route('users.destroy', $user) }}', '{{ $user->name }}')" title="Delete"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center cell-sub py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteUser(url, userName) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete User?',
                    text: 'Are you sure you want to delete user ' + userName + '? This action cannot be undone.',
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
                if (confirm('Delete this user?')) {
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
