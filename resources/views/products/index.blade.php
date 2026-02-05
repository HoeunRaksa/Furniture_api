@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Product Management</h5>
                    <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i> Add New Product
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Stock</th>
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
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.data') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'price', name: 'price' },
                    { data: 'discount', name: 'discount' },
                    { data: 'stock', name: 'stock' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'desc']],
            });

            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                if (confirm("Are you sure you want to delete this product?")) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                $('#productsTable').DataTable().ajax.reload();
                                toastr.success(response.msg);
                            } else {
                                toastr.error(response.msg);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
