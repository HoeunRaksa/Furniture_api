@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-widget title="Products Management">
                <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
                    <button type="button" id="deleteSelectedBtn" class="btn btn-danger rounded-pill px-4 shadow-sm d-none">
                        <i class="bi bi-trash me-2"></i> Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                    <a href="{{ route('products.create') }}" class="btn btn-primary rounded-1 px-4 shadow-sm hover-lift">
                        <i class="bi bi-plus-lg me-2"></i> Add New Product
                    </a>
                </div>

                @if($products->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted fw-bold">No products found in the database.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="productsTable">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th style="width: 40px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllProducts">
                                    </div>
                                </th>
                                <th class="py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Product</th>
                                <th class="py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Category</th>
                                <th class="py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Price</th>
                                <th class="py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Stock</th>
                                <th class="py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Status</th>
                                <th class="text-end py-3 px-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="px-3">
                                    <div class="form-check">
                                        <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product->id }}">
                                    </div>
                                </td>
                                <td class="px-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($product->images->first())
                                        <img src="{{ asset($product->images->first()->image_url) }}" class="rounded-1 border" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                        <div class="rounded-1 bg-light d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b;">{{ $product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3">
                                    @if($product->category)
                                    <span class="badge bg-light text-dark border">{{ $product->category->name }}</span>
                                    @else
                                    <span class="text-muted fst-italic">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-3 fw-bold" style="color: #333;">${{ number_format($product->price, 2) }}</td>
                                <td class="px-3">
                                    @if($product->stock > 10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-1">{{ $product->stock }} in stock</span>
                                    @elseif($product->stock > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-1">Low: {{ $product->stock }}</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-1">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="px-3">
                                    @if($product->is_active)
                                    <span class="badge bg-success rounded-1">Active</span>
                                    @else
                                    <span class="badge bg-secondary rounded-1">Inactive</span>
                                    @endif
                                    @if($product->is_featured)
                                    <span class="badge bg-warning text-dark border border-warning rounded-1 ms-1">Featured</span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    @php
                                    $canEdit = auth()->user()->hasPermission('edit_products');
                                    $canDelete = auth()->user()->hasPermission('delete_products');
                                    @endphp
                                    <div class="btn-group">
                                        <a href="{{ $canEdit ? route('products.edit', $product->id) : 'javascript:void(0)' }}"
                                            class="btn btn-sm btn-light border {{ $canEdit ? '' : 'permission-denied' }}"
                                            data-authorized="{{ $canEdit ? 'true' : 'false' }}"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button type="button"
                                            class="btn btn-sm btn-light border text-danger delete-product {{ $canDelete ? '' : 'permission-denied' }}"
                                            data-url="{{ route('products.destroy', $product->id) }}"
                                            data-authorized="{{ $canDelete ? 'true' : 'false' }}"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-widget>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#productsTable').DataTable({
            dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            order: [
                [1, 'asc']
            ],
            pageLength: 10,
            language: {
                search: "Search products:",
                lengthMenu: "Show _MENU_ products",
                info: "Showing _START_ to _END_ of _TOTAL_ products",
                infoEmpty: "No products available",
                infoFiltered: "(filtered from _MAX_ total products)"
            },
            columnDefs: [{
                orderable: false,
                targets: 0
            }]
        });

        // Instant Intercept for Unauthorized Actions (Universal)
        $(document).on('click', '.permission-denied', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            toastr.error('You do not have permission to perform this action.');
            return false;
        });

        // Batch delete logic
        $('#selectAllProducts').on('change', function() {
            $('.product-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        $(document).on('change', '.product-checkbox', function() {
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.product-checkbox:checked').length;
            $('#selectedCount').text(count);
            if (count > 0) {
                $('#deleteSelectedBtn').removeClass('d-none');
            } else {
                $('#deleteSelectedBtn').addClass('d-none');
                $('#selectAllProducts').prop('checked', false);
            }
        }

        $('#deleteSelectedBtn').on('click', function() {
            const ids = [];
            $('.product-checkbox:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length === 0) return;

            showConfirmModal(`Are you sure you want to delete ${ids.length} selected products?`, () => {
                const $btn = $('#deleteSelectedBtn');
                const originalContent = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                $.ajax({
                    url: "{{ route('products.mass-destroy') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(res.msg);
                            $btn.prop('disabled', false).html(originalContent);
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.msg || 'Error during mass deletion';
                        toastr.error(msg);
                        $btn.prop('disabled', false).html(originalContent);
                    }
                });
            });
        });

        // Delete confirmation
        $(document).on('click', '.delete-product', function(e) {
            e.preventDefault();
            if ($(this).data('authorized') === false) {
                toastr.error('You do not have permission to perform this action.');
                return;
            }
            const url = $(this).data('url');
            showConfirmModal('Are you sure you want to delete this product?', () => {
                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            toastr.error('Error deleting product');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('You need permission to perform this action.');
                    });
            });
        });
    });
</script>
@endpush
@endsection