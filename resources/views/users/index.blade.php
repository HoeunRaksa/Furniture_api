@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">User Management</h5>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="bi bi-person-plus me-2"></i> Add User
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="createUserForm">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Display Name</label>
                            <input type="text" name="name" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Email</label>
                            <input type="email" name="email" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Role</label>
                            <select name="role" class="form-select rounded-3">
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.data') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'full_name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'desc']]
            });

            $('#createUserForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('users.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $('#createUserForm')[0].reset();
                            $('#createUserModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            });

            $(document).on('click', '.delete-user', function() {
                const url = $(this).data('url');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.ajax({
                        url: url,
                        method: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.msg);
                                table.ajax.reload();
                            } else {
                                toastr.error(res.msg);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
