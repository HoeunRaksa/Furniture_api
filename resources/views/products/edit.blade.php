@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-sm rounded-circle me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="form_update_product" enctype="multipart/form-data" action="{{ route('products.update', $product->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- Left Column: Core Details --}}
                                <div class="col-lg-7">
                                    {{-- Basic Info Card --}}
                                    <div class="premium-card mb-4">
                                        <div class="card-header-premium">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            <span>General Information</span>
                                        </div>
                                        <div class="p-4">
                                            <div class="mb-4">
                                                <label class="form-label-premium">Product Name <span class="text-danger">*</span></label>
                                                <div class="input-group-premium">
                                                    <i class="bi bi-tag input-icon"></i>
                                                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label-premium">Category <span class="text-danger">*</span></label>
                                                <div class="input-group-premium">
                                                    <i class="bi bi-grid-fill input-icon"></i>
                                                    <select name="category_id" class="form-select" required>
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="active">
                                                                <i class="bi bi-check-circle me-1 text-success"></i> Active
                                                            </label>
                                                            <input type="checkbox" name="active" class="form-check-input" id="active" value="1" {{ $product->active ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="isFeatured">
                                                                <i class="bi bi-star-fill me-1 text-warning"></i> Featured
                                                            </label>
                                                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="isRecommended">
                                                                <i class="bi bi-hand-thumbs-up-fill me-1 text-info"></i> Recommend
                                                            </label>
                                                            <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended" value="1" {{ $product->is_recommended ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Description Card --}}
                                    <div class="premium-card mb-4">
                                        <div class="card-header-premium">
                                            <i class="bi bi-text-left me-2"></i>
                                            <span>Detailed Description</span>
                                        </div>
                                        <div class="p-4">
                                            <div class="mb-3">
                                                <textarea name="description" class="form-control rounded-4 mb-3" rows="3">{{ $product->description }}</textarea>
                                            </div>
                                            <div id="descriptionLines" class="mb-3">
                                                @foreach($product->descriptionLines as $line)
                                                <div class="description-line mb-2">
                                                    <div class="input-group-premium">
                                                        <i class="bi bi-dash input-icon"></i>
                                                        <input type="text" name="description_lines[]" class="form-control" value="{{ $line->text }}">
                                                        <button type="button" class="btn-remove-line remove-line">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <button type="button" id="addLine" class="btn btn-premium-secondary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i> Add Feature Line
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Image Card --}}
                                    <div class="premium-card">
                                        <div class="card-header-premium border-0">
                                            <i class="bi bi-image me-2"></i>
                                            <span>Product Visuals (Multiple)</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <div class="mb-3 d-flex flex-wrap gap-2" id="existingImages">
                                                @foreach($product->images as $img)
                                                    <div class="position-relative" id="img_container_{{ $img->id }}">
                                                        <img src="{{ asset($img->image_url) }}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 start-100 translate-middle delete-image" data-id="{{ $img->id }}" style="padding: 0 5px;">
                                                            <i class="bi bi-x small"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="image-upload-wrapper">
                                                <label class="upload-zone" for="productImages">
                                                    <div class="upload-icon">
                                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                                    </div>
                                                    <p class="m-0">Add more images (Max 5MB each)</p>
                                                    <input type="file" name="images[]" id="productImages" class="d-none" accept="image/*" multiple>
                                                </label>
                                            </div>
                                            <div class="mt-3 d-flex flex-wrap gap-2" id="imagePreviewContainer"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right Column: Attributes & Variants --}}
                                <div class="col-lg-5">
                                    {{-- Attributes Card --}}
                                    <div class="premium-card mb-4">
                                        <div class="card-header-premium">
                                            <i class="bi bi-sliders me-2"></i>
                                            <span>Dynamic Attributes</span>
                                        </div>
                                        <div class="p-4">
                                            <div id="attributesSection">
                                                @foreach ($attributes as $attr)
                                                    <div class="attribute-pill-group mb-4" data-id="{{ $attr->id }}">
                                                        <label class="form-label-premium mb-2">{{ $attr->name }}</label>
                                                        <div class="attribute-options">
                                                            @foreach ($attr->values as $val)
                                                                @php
                                                                    $isConnected = $product->variants->some(fn($v) => $v->attributes->contains('id', $val->id));
                                                                @endphp
                                                                <div class="attr-checkbox">
                                                                    <input class="attribute-check" type="checkbox"
                                                                        value="{{ $val->id }}"
                                                                        id="attr_{{ $attr->id }}_{{ $val->id }}"
                                                                        {{ $isConnected ? 'checked' : '' }}>
                                                                    <label class="attr-label" for="attr_{{ $attr->id }}_{{ $val->id }}">
                                                                        {{ $val->value }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" id="generateVariants" class="btn btn-premium-primary w-100">
                                                <i class="bi bi-magic me-2"></i> Regenerate Combinations
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Variants Card --}}
                                    <div class="premium-card mb-4">
                                        <div class="card-header-premium">
                                            <i class="bi bi-stack me-2"></i>
                                            <span>Inventory Variants</span>
                                        </div>
                                        <div class="p-4">
                                            <div id="variantsSection" class="variants-container">
                                                @foreach($product->variants as $index => $variant)
                                                    <div class="variant-premium-card">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="variant-badges-container">
                                                                @foreach($variant->attributes as $val)
                                                                    <span class="variant-badge-pill">{{ $val->attribute->name }}: {{ $val->value }}</span>
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][]" value="{{ $val->id }}">
                                                                @endforeach
                                                            </div>
                                                            <button type="button" class="btn btn-sm text-danger remove-variant p-0"><i class="bi bi-trash3"></i></button>
                                                        </div>
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <input type="text" name="variants[{{ $index }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}" placeholder="SKU">
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $variant->price }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Discount Card --}}
                                    <div class="premium-card">
                                        <div class="card-header-premium">
                                            <i class="bi bi-percent me-2"></i>
                                            <span>Promotion Details</span>
                                        </div>
                                        <div class="p-4">
                                            @php $discount = $product->discounts->where('active', true)->first(); @endphp
                                            <div class="mb-3">
                                                <label class="form-label-premium">Discount Title</label>
                                                <div class="input-group-premium">
                                                    <i class="bi bi-pencil-square input-icon"></i>
                                                    <input type="text" name="discount_name" class="form-control" value="{{ $discount?->name }}" placeholder="Summer Sale">
                                                </div>
                                            </div>
                                            <div class="row g-3 align-items-end">
                                                <div class="col-8">
                                                    <label class="form-label-premium">Discount Value</label>
                                                    <div class="input-group-premium">
                                                        <i class="bi bi-cash-stack input-icon"></i>
                                                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ $discount?->value }}" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="is_percentage" class="form-select border-2 py-2">
                                                        <option value="1" {{ ($discount?->is_percentage ?? true) ? 'selected' : '' }}>%</option>
                                                        <option value="0" {{ !($discount?->is_percentage ?? true) ? 'selected' : '' }}>$</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Final Submit --}}
                            <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3 align-items-center">
                                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" id="btnSubmitProduct" class="btn btn-primary rounded-pill px-5 fw-bold">
                                    Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .premium-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; }
        .card-header-premium { background: #f8fafc; padding: 14px 24px; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        .form-label-premium { font-weight: 600; font-size: 0.8rem; color: #64748b; margin-bottom: 8px; display: block; text-transform: uppercase; }
        .input-group-premium { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 14px; color: #94a3b8; z-index: 10; }
        .input-group-premium .form-control, .input-group-premium .form-select { padding-left: 42px; border: 2px solid #e2e8f0; border-radius: 14px; height: 45px; }
        .status-toggle-card { background: #fff; border: 2px solid #e2e8f0; border-radius: 14px; padding: 10px 16px; }
        .btn-remove-line { position: absolute; right: 8px; background: none; border: none; color: #f43f5e; font-size: 1.2rem; }
        .image-upload-wrapper { border: 2px dashed #cbd5e1; border-radius: 20px; padding: 20px; text-align: center; cursor: pointer; }
        .attribute-options { display: flex; flex-wrap: wrap; gap: 10px; }
        .attr-checkbox { position: relative; }
        .attr-checkbox input { position: absolute; opacity: 0; cursor: pointer; }
        .attr-label { background: white; border: 2px solid #e2e8f0; padding: 6px 16px; border-radius: 30px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
        .attr-checkbox input:checked + .attr-label { background: #667eea; border-color: #667eea; color: white; }
        .variant-premium-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 15px; }
        .variant-badge-pill { display: inline-block; background: #fff; color: #475569; font-weight: 700; font-size: 0.7rem; padding: 3px 10px; border-radius: 6px; margin-right: 5px; border: 1px solid #e2e8f0; text-transform: uppercase; }
        .btn-premium-primary { background: #667eea; color: white; border-radius: 14px; padding: 10px; font-weight: 700; border: none; }
        .btn-premium-secondary { background: #f1f5f9; color: #667eea; border-radius: 14px; padding: 6px 15px; font-weight: 700; border: none; }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Add description line
            $('#addLine').click(function() {
                const $newLine = $(`
                    <div class="description-line mb-2">
                        <div class="input-group-premium">
                            <i class="bi bi-dash input-icon"></i>
                            <input type="text" name="description_lines[]" class="form-control" placeholder="Description feature line">
                            <button type="button" class="btn-remove-line remove-line">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                $('#descriptionLines').append($newLine);
            });

            $(document).on('click', '.remove-line', function() {
                $(this).closest('.description-line').remove();
            });

            // Image handling (Multiple)
            $('#productImages').on('change', function(e) {
                $('#imagePreviewContainer').empty();
                const files = e.target.files;
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        $('#imagePreviewContainer').append(`
                            <div class="position-relative">
                                <img src="${re.target.result}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                        `);
                    }
                    reader.readAsDataURL(files[i]);
                }
            });

            // Delete Image
            $(document).on('click', '.delete-image', function() {
                const id = $(this).data('id');
                const $container = $(`#img_container_${id}`);
                showConfirmModal("Delete this image?", () => {
                    $.post("{{ url('products/image/delete') }}/" + id, { _token: "{{ csrf_token() }}", _method: 'DELETE' }, function(res) {
                        toastr.success(res.msg);
                        $container.remove();
                    });
                });
            });

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
            }

            // Generate variants
            $('#generateVariants').click(function() {
                let selected = {};
                $('.attribute-pill-group').each(function() {
                    let attrId = $(this).data('id');
                    let attrName = $(this).find('.form-label-premium').text().split('\n')[0].trim();
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({ id: $(this).val(), name: $(this).next('.attr-label').text().trim(), attrName: attrName });
                    });
                    if (vals.length) selected[attrId] = vals;
                });

                let keys = Object.keys(selected);
                if (!keys.length) { toastr.warning('Please select attributes first!'); return; }

                $('#variantsSection').empty();

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let badges = combo.map(c => `<span class="variant-badge-pill">${c.attrName}: ${c.name}</span>`).join('');
                    let hiddenInputs = combo.map(c => `<input type="hidden" name="variants[${index}][attributes][]" value="${c.id}">`).join('');
                    $('#variantsSection').append(`
                        <div class="variant-premium-card">
                            <div class="mb-2">${badges}</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU">
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.01" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="Price" required>
                                </div>
                            </div>
                            ${hiddenInputs}
                        </div>
                    `);
                });
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-premium-card').remove();
            });

            // Form submission
            $('#form_update_product').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitProduct');
                $btn.prop('disabled', true).text('Updating...');
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.msg);
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).text('Update Product');
                        if(err.status === 422) {
                            const errors = err.responseJSON.errors;
                            Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                        } else {
                            toastr.error('Error updating product.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
