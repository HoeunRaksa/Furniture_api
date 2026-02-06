@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @include('categories.edit')
    @include('categories.create')

    <x-widget title="Category List">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <button type="button" id="deleteSelectedBtn" class="btn btn-danger rounded-pill px-4 shadow-sm d-none">
                <i class="bi bi-trash me-2"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg me-2"></i> Create New Category
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="categoriesTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllCategories">
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-widget>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#categoriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('categories.data') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
        });

        // Handle "Select All"
        $('#selectAllCategories').on('change', function() {
            $('.category-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        // Handle individual checkbox change
        $(document).on('change', '.category-checkbox', function() {
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.category-checkbox:checked').length;
            $('#selectedCount').text(count);
            if (count > 0) {
                $('#deleteSelectedBtn').removeClass('d-none');
            } else {
                $('#deleteSelectedBtn').addClass('d-none');
                $('#selectAllCategories').prop('checked', false);
            }
        }

        // Delete Selected
        $('#deleteSelectedBtn').on('click', function() {
            const ids = [];
            $('.category-checkbox:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length === 0) return;

            showConfirmModal(`Are you sure you want to delete ${ids.length} selected categories?`, () => {
                const $btn = $('#deleteSelectedBtn');
                const originalContent = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                $.ajax({
                    url: "{{ route('categories.mass-destroy') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $('#categoriesTable').DataTable().ajax.reload();
                            $('#deleteSelectedBtn').addClass('d-none');
                            $('#selectAllCategories').prop('checked', false);
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

        // Delete Category
        $(document).on('click', '.delete-category', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            showConfirmModal("Are you sure you want to delete this category?", () => {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#categoriesTable').DataTable().ajax.reload();
                            toastr.success(response.msg);
                            updateSelectedCount();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            toastr.error('You need permission to perform this action.');
                        } else {
                            const msg = xhr.responseJSON?.msg || 'Error deleting category';
                            toastr.error(msg);
                        }
                    }
                });
            });
        });

        // Edit Category Modal setup
        $(document).on('click', '.edit-category', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let $modal = $('#editCategoryModal');

            $modal.find('input[name="name"]').val(name);
            $modal.find('input[name="cate_id"]').val(id);
            $modal.modal('show');

            $modal.find('form').off('submit').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/categories/update/' + id,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $modal.modal('hide');
                            $('#categoriesTable').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            toastr.error('You need permission to perform this action.');
                        } else {
                            toastr.error('Error updating category');
                        }
                    }
                });
            });
        });

        // Create Category
        $('#createCategoryModal form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('categories.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        $('#createCategoryModal form')[0].reset();
                        $('#createCategoryModal').modal('hide');
                        $('#categoriesTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(res.msg);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        toastr.error('You need permission to perform this action.');
                    } else {
                        const msg = xhr.responseJSON?.msg || 'Error creating category';
                        toastr.error(msg);
                    }
                }
            });
        });

        // Reset CREATE category modal when closed
        $('#createCategoryModal').on('hidden.bs.modal', function() {
            $('#createCategoryModal form')[0].reset();
        });

        // Reset EDIT category modal when closed
        $('#editCategoryModal').on('hidden.bs.modal', function() {
            $('#editCategoryModal form')[0].reset();
        });
    });
</script>
@endpush