@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-sm rounded-circle me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="productForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <h6 class="fw-bold mb-3 text-primary">Basic Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Product Name</label>
                                        <input type="text" name="name" class="form-control rounded-3" value="{{ $product->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Category</label>
                                        <select name="category_id" class="form-select rounded-3" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="description" class="form-control rounded-3" rows="5">{{ $product->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-3 text-primary">Pricing & Stock</h6>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Price ($)</label>
                                            <input type="number" step="0.01" name="price" class="form-control rounded-3" value="{{ $product->price }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Stock</label>
                                            <input type="number" name="stock" class="form-control rounded-3" value="{{ $product->stock }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Discount (%)</label>
                                            <input type="number" name="discount" class="form-control rounded-3" value="{{ $product->discount }}">
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mt-4 mb-3 text-primary">Product Images</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Add More Images</label>
                                        <input type="file" name="images[]" class="form-control rounded-3" multiple accept="image/*" id="imageInput">
                                        <div class="mt-3 d-flex flex-wrap gap-2" id="currentImages">
                                            @foreach($product->images as $image)
                                                <div class="position-relative current-image-box" id="img-{{ $image->id }}">
                                                    <img src="{{ asset($image->image_url) }}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 translate-middle p-1 border border-white delete-product-image" 
                                                            data-id="{{ $image->id }}">
                                                        <i class="bi bi-x small"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold" id="submitBtn">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.delete-product-image', function() {
            const id = $(this).data('id');
            const $box = $(`#img-${id}`);
            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: "{{ url('products/image') }}/" + id,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $box.fadeOut(300, function() { $(this).remove(); });
                        } else {
                            toastr.error(res.msg);
                        }
                    }
                });
            }
        });

        $(document).ready(function() {
            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#submitBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

                $.ajax({
                    url: "{{ route('products.update', $product->id) }}",
                    method: 'POST', // Spoofed to PUT via @method
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            window.location.href = res.location;
                        } else {
                            toastr.error(res.msg);
                            $btn.prop('disabled', false).text('Update Product');
                        }
                    },
                    error: function(err) {
                        toastr.error('Error occurred while updating product');
                        $btn.prop('disabled', false).text('Update Product');
                    }
                });
            });
        });
    </script>
@endpush
