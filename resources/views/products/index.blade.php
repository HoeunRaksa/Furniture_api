@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Products Management</h5>
                        <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift">
                            <i class="bi bi-plus-lg me-2"></i> Add New Product
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsTable" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="30%">Product</th>
                                        <th width="15%">Category</th>
                                        <th width="10%">Price</th>
                                        <th width="10%">Discount</th>
                                        <th width="10%">Stock</th>
                                        <th width="10%">Featured</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #productsTable { font-size: 0.9rem; }
        #productsTable thead th {
            background: #f8fafc;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #475569;
        }
        #productsTable tbody tr:hover { background-color: rgba(102, 126, 234, 0.05) !important; }
        #productsTable tbody td { padding: 15px 12px; vertical-align: middle; }
        .product-image { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-info { display: flex; align-items: center; gap: 12px; }
        .status-badge { font-size: 0.7rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.data') }}",
                    error: function (xhr, error, thrown) {
                        console.error('DataTables Error:', error, thrown);
                        toastr.error('Failed to load products. Please check console.');
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            let imageUrl = row.image_url ? row.image_url : '/placeholder.png';
                            return `
                                <div class="product-info">
                                    <img src="${imageUrl}" class="product-image" alt="${data}">
                                    <div class="fw-bold text-dark">${data}</div>
                                </div>
                            `;
                        }
                    },
                    { data: 'category', name: 'category.name' },
                    { data: 'price', name: 'price' },
                    { data: 'discount', name: 'discount' },
                    { data: 'stock', name: 'stock' },
                    { data: 'featured', name: 'is_featured' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: { search: "_INPUT_", searchPlaceholder: "Search products..." }
            });

            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                showConfirmModal("Are you sure you want to delete this product?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            $('#productsTable').DataTable().ajax.reload();
                            toastr.success(res.msg || 'Product deleted successfully');
                        },
                        error: function() {
                            toastr.error('Failed to delete product');
                        }
                    });
                });
            });
        });
    </script>
@endsection
