@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0">Create New Product</h5>
                </div>
                <form id="form_add_product" enctype="multipart/form-data">
                            @csrf

                            <x-widget title="General Information">
                                <div class="p-2">
                                    <div class="mb-4">
                                        <label class="form-label-premium">Product Name <span class="text-danger">*</span></label>
                                        <div class="input-group-premium">
                                            <i class="bi bi-tag input-icon"></i>
                                            <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-premium">Category <span class="text-danger">*</span></label>
                                        <div class="input-group-premium">
                                            <i class="bi bi-grid-fill input-icon"></i>
                                            <select name="category_id" class="form-select" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-4 g-3">
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Price ($)</label>
                                            <div class="input-group-premium">
                                                <i class="bi bi-currency-dollar input-icon"></i>
                                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Discount ($)</label>
                                            <div class="input-group-premium">
                                                <i class="bi bi-tag-fill input-icon"></i>
                                                <input type="number" step="0.01" name="discount" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-premium">Total Stock Availability</label>
                                        <div class="input-group-premium">
                                            <i class="bi bi-archive input-icon"></i>
                                            <input type="number" name="stock" class="form-control" placeholder="Enter total stock quantity" value="0">
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="status-toggle-card">
                                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                    <label class="form-check-label fw-bold m-0" for="is_active">
                                                        <i class="bi bi-check-circle me-1 text-success"></i> Active
                                                    </label>
                                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="status-toggle-card">
                                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                    <label class="form-check-label fw-bold m-0" for="is_featured">
                                                        <i class="bi bi-star-fill me-1 text-warning"></i> Featured
                                                    </label>
                                                    <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-widget>

                            <x-widget title="Product Description">
                                <div class="p-2">
                                    <textarea name="description" class="form-control rounded-4" rows="5" placeholder="Enter product description..."></textarea>
                                </div>
                            </x-widget>

                            <x-widget title="Product Visuals (Gallery)">
                                <div class="p-2">
                                    <div class="image-upload-wrapper">
                                        <label class="upload-zone" for="productImages">
                                            <div class="upload-icon">
                                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                            </div>
                                            <p class="m-0">Click to select multiple images (Max 5MB each)</p>
                                            <input type="file" name="images[]" id="productImages" class="d-none" accept="image/*" multiple>
                                        </label>
                                    </div>
                                    <div class="mt-3 d-flex flex-wrap gap-2" id="imagePreviewContainer"></div>
                                </div>
                            </x-widget>

                            <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3 align-items-center">
                                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" id="btnSubmitProduct" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                    Save Product
                                </button>
                            </div>
                        </form>
                </div>
            </div>
        </div>

    <style>
        .form-label-premium { font-weight: 600; font-size: 0.8rem; color: #64748b; margin-bottom: 8px; display: block; text-transform: uppercase; }
        .input-group-premium { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 14px; color: #94a3b8; z-index: 10; }
        .input-group-premium .form-control, .input-group-premium .form-select { padding-left: 42px; border: 2px solid #e2e8f0; border-radius: 14px; height: 45px; }
        .status-toggle-card { background: #fff; border: 2px solid #e2e8f0; border-radius: 14px; padding: 10px 16px; }
        .image-upload-wrapper { border: 2px dashed #cbd5e1; border-radius: 20px; padding: 20px; text-align: center; cursor: pointer; }
        .upload-icon { font-size: 2.5rem; color: #667eea; }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Image handling (Multiple)
            $('#productImages').on('change', function(e) {
                $('#imagePreviewContainer').empty();
                const files = e.target.files;
                if (files.length > 0) {
                    $('.image-upload-wrapper').addClass('border-primary');
                }
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        $('#imagePreviewContainer').append(`
                            <div class="position-relative animate__animated animate__fadeIn">
                                <img src="${re.target.result}" class="rounded-3 shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid white;">
                            </div>
                        `);
                    }
                    reader.readAsDataURL(files[i]);
                }
            });

            // Form submission
            $('#form_add_product').on('submit', function(e) {
                e.preventDefault();

                const $btn = $('#btnSubmitProduct');
                const oldContent = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                
                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.msg || 'Success!');
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(oldContent);
                        if(err.status === 422) {
                            const errors = err.responseJSON.errors;
                            Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                        } else {
                            toastr.error('Error while saving product.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
