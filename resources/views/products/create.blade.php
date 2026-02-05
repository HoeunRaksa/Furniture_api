@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <x-widget title="Create New Product">
        <form id="form_add_product" enctype="multipart/form-data">
            @csrf

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('products.index') }}" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Back">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>

            <!-- General Information -->
            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">General Information</h6>
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

            <hr class="my-4 border-light">

            <!-- Description -->
            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Product Description</h6>
            <div class="p-2">
                <textarea name="description" class="form-control rounded-4" rows="5" placeholder="Enter product description..."></textarea>
            </div>

            <hr class="my-4 border-light">

            <!-- Visuals -->
            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Product Visuals (Gallery)</h6>
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

            <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3 align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                <button type="submit" id="btnSubmitProduct" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    Save Product
                </button>
            </div>
        </form>
    </x-widget>
</div>

<style>
    .form-label-premium {
        font-weight: 700;
        font-size: 0.75rem;
        color: #333;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .input-group-premium {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        z-index: 10;
    }

    .input-group-premium .form-control,
    .input-group-premium .form-select {
        padding-left: 42px;
        border: 1px solid #d1d5db;
        border-radius: 2px;
        height: 45px;
        font-family: var(--font-primary);
    }

    .input-group-premium .form-control:focus,
    .input-group-premium .form-select:focus {
        border-color: var(--color-gold);
        box-shadow: none;
    }

    .status-toggle-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 2px;
        padding: 10px 16px;
    }

    .image-upload-wrapper {
        border: 2px dashed #cbd5e1;
        border-radius: 2px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-upload-wrapper:hover {
        border-color: var(--color-gold);
        background: #fafafa;
    }

    .upload-icon {
        font-size: 2.5rem;
        color: var(--color-gold);
    }
</style>
@endsection

@push('scripts')
<script>
    $(function() {
        let uploadedFiles = [];

        // Image handling (Multiple) with Custom Array
        $('#productImages').on('change', function(e) {
            const files = Array.from(e.target.files);

            // Add new files to our array
            files.forEach(file => {
                uploadedFiles.push(file);

                const reader = new FileReader();
                reader.onload = function(re) {
                    // Create ID based on timestamp and name to identify file later if needed
                    const fileId = Date.now() + '_' + file.name.replace(/\s/g, '');
                    file.tempId = fileId; // Attach temp ID to file object if browser allows, or rely on index

                    $('#imagePreviewContainer').append(`
                        <div class="position-relative d-inline-block me-2 mb-2" id="preview-${fileId}">
                            <img src="${re.target.result}" class="rounded-3 shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                style="width: 20px; height: 20px;" 
                                onclick="removeNewFile('${fileId}')">
                                <i class="bi bi-x" style="font-size: 1rem;"></i>
                            </button>
                        </div>
                    `);
                }
                reader.readAsDataURL(file);
            });

            // Reset the input value so the same file selected again triggers change if needed
            // But usually we just keep accumulating in uploadedFiles
            $(this).val('');

            if (uploadedFiles.length > 0) {
                $('.image-upload-wrapper').addClass('border-primary');
            }
        });

        // Global function to remove new file
        window.removeNewFile = function(fileId) {
            uploadedFiles = uploadedFiles.filter(f => f.tempId !== fileId);
            $(`#preview-${fileId}`).remove();
        };

        // Form submission
        $('#form_add_product').on('submit', function(e) {
            e.preventDefault();

            const $btn = $('#btnSubmitProduct');
            const oldContent = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            // Create FormData manually
            var formData = new FormData(this);

            // Remove the empty 'images[]' from original input if present
            formData.delete('images[]');

            // Append all files from our custom array
            uploadedFiles.forEach(file => {
                formData.append('images[]', file);
            });

            $.ajax({
                url: "{{ route('products.store') }}",
                method: 'POST',
                data: formData,
                processData: false, // Important
                contentType: false, // Important
                success: function(res) {
                    toastr.success(res.msg || 'Success!');
                    window.location.href = res.location;
                },
                error: function(err) {
                    $btn.prop('disabled', false).html(oldContent);
                    if (err.status === 422) {
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
@endpush