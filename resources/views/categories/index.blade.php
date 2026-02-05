@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        @include('categories.edit')
        @include('categories.create')
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Category List</h5>
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="bi bi-plus-lg me-2"></i> Create New Category
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="categoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.data') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'desc']],
            });

            // Delete Category
            $(document).on('click', '.delete-category', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                if (confirm("Are you sure you want to delete this category?")) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                $('#categoriesTable').DataTable().ajax.reload();
                                toastr.success(response.msg);
                            } else {
                                toastr.error(response.msg);
                            }
                        }
                    });
                }
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
                    }
                });
            });
        });
    </script>
@endpush
