@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <x-widget title="User Management">
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus me-2"></i> Add User
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th style="width: 100px;">Role</th>
                        <th style="width: 80px;" class="text-center">Status</th>
                        <th style="width: 80px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-widget>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="createUserForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="createUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Username</label>
                        <input type="text" name="username" class="form-control rounded-3" required>
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
                            <option value="user" selected>User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_is_active" checked>
                            <label class="form-check-label fw-bold small text-muted" for="create_is_active">Active</label>
                        </div>
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

<!-- Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="editUserForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
                <input type="hidden" id="edit_is_last_admin" value="0">
                <input type="hidden" id="edit_current_role">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3 text-center">
                        <img id="edit_avatar_preview" src="" alt="Preview" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Profile Image</label>
                        <input type="file" id="edit_profile_image" name="profile_image" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Username</label>
                        <input type="text" id="edit_username" name="username" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" id="edit_password" name="password" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Role</label>
                        <select id="edit_role" name="role" class="form-select rounded-3">
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="user">User</option>
                        </select>
                        <div id="last_admin_warning" class="alert alert-warning mt-2" style="display: none;">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> You are the last admin! Changing your role will lock you out of admin functions.
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label fw-bold small text-muted" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
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
            dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'avatar',
                    name: 'username',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
            language: {
                paginate: {
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                }
            }
        });

        // Create user
        $('#createUserForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).text('Creating...');

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('users.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        $('#createUserForm')[0].reset();
                        $('#create_image_preview').remove();
                        $('#createUserModal').modal('hide');
                        table.ajax.reload();
                    }
                    $btn.prop('disabled', false).text('Create');
                },
                error: function() {
                    toastr.error('Error creating user');
                    $btn.prop('disabled', false).text('Create');
                }
            });
        });

        // Image preview for CREATE modal
        $('input[name="profile_image"]').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = $('#create_image_preview');
                    if (preview.length === 0) {
                        $('input[name="profile_image"]').after(
                            '<div id="create_image_preview" class="mt-2 text-center">' +
                            '<img src="" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">' +
                            '</div>'
                        );
                        preview = $('#create_image_preview');
                    }
                    preview.find('img').attr('src', e.target.result);
                    preview.show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Edit user - load data
        $(document).on('click', '.edit-user', function() {
            const userId = $(this).data('id');
            $.ajax({
                url: `/users/${userId}/edit`,
                method: 'GET',
                success: function(res) {
                    if (res.success) {
                        $('#edit_user_id').val(res.user.id);
                        $('#edit_username').val(res.user.username);
                        $('#edit_email').val(res.user.email);
                        $('#edit_role').val(res.user.role);
                        $('#edit_is_active').prop('checked', res.user.is_active);
                        $('#edit_password').val('');
                        $('#edit_current_role').val(res.user.role);

                        // Set avatar preview
                        const avatarUrl = res.user.profile_image ?
                            '{{ asset("") }}' + res.user.profile_image :
                            '{{ asset("images/default-avatar.png") }}';
                        $('#edit_avatar_preview').attr('src', avatarUrl);

                        // Check if last admin
                        const isLastAdmin = res.user.role === 'admin' && res.admin_count === 1;
                        $('#edit_is_last_admin').val(isLastAdmin ? '1' : '0');

                        if (isLastAdmin) {
                            $('#last_admin_warning').hide();
                        }

                        $('#editUserModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('Error loading user data');
                }
            });
        });

        // Image preview for EDIT modal
        $('#edit_profile_image').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit_avatar_preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Show warning when last admin tries to change role
        $('#edit_role').on('change', function() {
            const isLastAdmin = $('#edit_is_last_admin').val() === '1';
            const currentRole = $('#edit_current_role').val();
            const newRole = $(this).val();

            if (isLastAdmin && currentRole === 'admin' && newRole !== 'admin') {
                $('#last_admin_warning').slideDown();
            } else {
                $('#last_admin_warning').slideUp();
            }
        });

        // Update user
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const userId = $('#edit_user_id').val();
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).text('Updating...');

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/users/${userId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        $('#editUserModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        toastr.error(res.msg);
                    }
                    $btn.prop('disabled', false).text('Update');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.msg || 'Error updating user';
                    toastr.error(msg);
                    $btn.prop('disabled', false).text('Update');
                }
            });
        });

        // Delete user
        $(document).on('click', '.delete-user', function() {
            const url = $(this).data('url');
            showConfirmModal("Delete this user? This action cannot be undone.", () => {
                $.ajax({
                    url: url,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            table.ajax.reload();
                        } else {
                            toastr.error(res.msg);
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.msg || 'Error deleting user';
                        toastr.error(msg);
                    }
                });
            });
        });
    });
</script>
@endpush