@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-widget title="Products Management">
                <div class="d-flex justify-content-end mb-3">
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
                    <table class="table table-hover align-middle" id="productsTableRaw">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr>
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
                                            <small class="text-muted">ID: {{ $product->id }}</small>
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
                                    <div class="btn-group">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger delete-product" data-url="{{ route('products.destroy', $product->id) }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 px-3">
                    {{ $products->links() }}
                </div>
                @endif
            </x-widget>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple delete confirmation
        const deleteBtns = document.querySelectorAll('.delete-product');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
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
                                alert('Error deleting product');
                            }
                        });
                });
            });
        });
    });
</script>
@endsection