@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <x-widget title="User Management">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <button type="button" id="deleteSelectedBtn" class="btn btn-danger rounded-pill px-4 shadow-sm d-none"
                data-authorized="{{ auth()->user()->hasPermission('delete_users') ? 'true' : 'false' }}"
                title="{{ auth()->user()->hasPermission('delete_users') ? 'Delete Selected' : 'You do not have permission to perform this action' }}">
                <i class="bi bi-trash me-2"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal"
                data-authorized="{{ auth()->user()->hasPermission('create_users') ? 'true' : 'false' }}"
                title="{{ auth()->user()->hasPermission('create_users') ? 'Add User' : 'You do not have permission to perform this action' }}">
                <i class="bi bi-person-plus me-2"></i> Add User
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllUsers">
                            </div>
                        </th>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-mdb-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" data-mdb-dismiss="modal">Cancel</button>
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
            dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            columns: [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
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
                [1, 'asc']
            ],
            language: {
                paginate: {
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                }
            },
            drawCallback: function() {
                updateSelectedCount();
            }
        });

        // Handle "Select All"
        $('#selectAllUsers').on('change', function() {
            $('.user-checkbox:not(:disabled)').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        // Handle individual checkbox change
        $(document).on('change', '.user-checkbox', function() {
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.user-checkbox:checked').length;
            $('#selectedCount').text(count);
            if (count > 0) {
                $('#deleteSelectedBtn').removeClass('d-none');
            } else {
                $('#deleteSelectedBtn').addClass('d-none');
                $('#selectAllUsers').prop('checked', false);
            }
        }

        // Delete Selected
        $('#deleteSelectedBtn').on('click', function() {
            if ($(this).data('authorized') === false) {
                toastr.error('You do not have permission to perform this action.');
                return;
            }
            const ids = [];
            $('.user-checkbox:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length === 0) return;

            showConfirmModal(`Are you sure you want to delete ${ids.length} selected users?`, () => {
                const $btn = $('#deleteSelectedBtn');
                const originalContent = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                $.ajax({
                    url: "{{ route('users.mass-destroy') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(res.msg);
                        }
                        $btn.prop('disabled', false).html(originalContent);
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            toastr.error('You need permission to perform this action.');
                        } else {
                            const msg = xhr.responseJSON?.msg || 'Error during mass deletion';
                            toastr.error(msg);
                        }
                        $btn.prop('disabled', false).html(originalContent);
                    }
                });
            });
        });

        // Create user
        $('#createUserForm').on('submit', function(e) {
            e.preventDefault();
            // Check authorization on the button triggering the modal or implied context
            // Since this is a form submit, we check if the button that opened it was authorized
            const $triggerBtn = $('button[data-bs-target="#createUserModal"]');
            if ($triggerBtn.length && $triggerBtn.data('authorized') === false) {
                toastr.error('You do not have permission to perform this action.');
                return;
            }

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
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        Object.values(errors).forEach(err => {
                            errorMsg += err[0] + '<br>';
                        });
                        toastr.error(errorMsg, 'Validation Error', {
                            enableHtml: true
                        });
                    } else {
                        const msg = xhr.responseJSON?.msg || 'Error creating user';
                        toastr.error(msg);
                    }
                    $btn.prop('disabled', false).text('Create');
                }
            });
        });

        // Image preview for CREATE modal
        $('#createUserForm input[name="profile_image"]').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = $('#create_image_preview');
                    if (preview.length === 0) {
                        $('#createUserForm input[name="profile_image"]').after(
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

        // Delete user
        $(document).on('click', '.delete-user', function() {
            if ($(this).data('authorized') === false) {
                toastr.error('You do not have permission to perform this action.');
                return;
            }
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
                        if (xhr.status === 403) {
                            toastr.error('You need permission to perform this action.');
                        } else {
                            const msg = xhr.responseJSON?.msg || 'Error deleting user';
                            toastr.error(msg);
                        }
                    }
                });
            });
        });
    });
</script>
@endpush