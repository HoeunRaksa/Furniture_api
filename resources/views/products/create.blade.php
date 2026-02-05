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
                        <h5 class="fw-bold mb-0">Create New Advanced Product</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="form_add_product" enctype="multipart/form-data">
                            @csrf

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

                                            <div class="row g-3">
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="active">
                                                                <i class="bi bi-check-circle me-1 text-success"></i> Active
                                                            </label>
                                                            <input type="checkbox" name="active" class="form-check-input" id="active" value="1" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="isFeatured">
                                                                <i class="bi bi-star-fill me-1 text-warning"></i> Featured
                                                            </label>
                                                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="status-toggle-card">
                                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                            <label class="form-check-label fw-bold m-0" for="isRecommended">
                                                                <i class="bi bi-hand-thumbs-up-fill me-1 text-info"></i> Recommend
                                                            </label>
                                                            <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended" value="1">
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
                                                <textarea name="description" class="form-control rounded-4 mb-3" rows="3" placeholder="General description..."></textarea>
                                            </div>
                                            <div id="descriptionLines" class="mb-3">
                                                <div class="description-line mb-2">
                                                    <div class="input-group-premium">
                                                        <i class="bi bi-dash input-icon"></i>
                                                        <input type="text" name="description_lines[]" class="form-control" placeholder="E.g. 100% Organic Cotton">
                                                        <button type="button" class="btn-remove-line remove-line">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" id="addLine" class="btn btn-premium-secondary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i> Add Feature Line
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Image Card --}}
                                    <div class="premium-card">
                                        <div class="card-header-premium">
                                            <i class="bi bi-image me-2"></i>
                                            <span>Product Visuals (Multiple)</span>
                                        </div>
                                        <div class="p-4">
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
                                                                <div class="attr-checkbox">
                                                                    <input class="attribute-check" type="checkbox"
                                                                        value="{{ $val->id }}"
                                                                        id="attr_{{ $attr->id }}_{{ $val->id }}">
                                                                    <label class="attr-label" for="attr_{{ $attr->id }}_{{ $val->id }}">
                                                                        {{ $val->value }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="alert alert-info border-0 rounded-4 p-3 small">
                                                <i class="bi bi-info-circle-fill me-2"></i>
                                                Select attributes, then generate combinations.
                                            </div>
                                            <button type="button" id="generateVariants" class="btn btn-premium-primary w-100">
                                                <i class="bi bi-magic me-2"></i> Generate Combinations
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
                                                <div class="text-center py-4 text-muted" id="noVariantsPlaceholder">
                                                    <i class="bi bi-layers fs-1 opacity-25 mb-2 d-block"></i>
                                                    <p class="small">No variants generated yet.</p>
                                                </div>
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
                                            <div class="mb-3">
                                                <label class="form-label-premium">Discount Title</label>
                                                <div class="input-group-premium">
                                                    <i class="bi bi-pencil-square input-icon"></i>
                                                    <input type="text" name="discount_name" class="form-control" placeholder="Summer Sale">
                                                </div>
                                            </div>
                                            <div class="row g-3 align-items-end">
                                                <div class="col-8">
                                                    <label class="form-label-premium">Discount Value</label>
                                                    <div class="input-group-premium">
                                                        <i class="bi bi-cash-stack input-icon"></i>
                                                        <input type="number" step="0.01" name="discount_value" class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="is_percentage" class="form-select border-2 py-2">
                                                        <option value="1">%</option>
                                                        <option value="0">$</option>
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
                                    Save Product
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
        .upload-icon { font-size: 2.5rem; color: #667eea; }
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

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
            }

            // Generate variants
            $('#generateVariants').click(function() {
                let selected = {};
                $('.attribute-pill-group').each(function() {
                    let attrId = $(this).data('id');
                    let attrName = $(this).find('span').first().text();
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({ id: $(this).val(), name: $(this).next('.attr-label').text().trim(), attrName: attrName });
                    });
                    if (vals.length) selected[attrId] = vals;
                });

                let keys = Object.keys(selected);
                if (!keys.length) { toastr.warning('Please select attributes first!'); return; }

                $('#noVariantsPlaceholder').hide();
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

            // Form submission
            $('#form_add_product').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitProduct');
                $btn.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.msg);
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).text('Save Product');
                        if(err.status === 422) {
                            const errors = err.responseJSON.errors;
                            Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                        } else {
                            toastr.error('Error creating product.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
