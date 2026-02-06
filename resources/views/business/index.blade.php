@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-widget title="Business Settings">
                <form id="businessSettingsForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-4 text-center">
                            <label class="form-label d-block fw-bold small text-muted">Business Logo</label>
                            <div class="position-relative d-inline-block">
                                <img src="{{ $business->logo_url ?? asset('placeholder.png') }}" id="logoPreview" class="rounded shadow-sm" style="width: 150px; height: 150px; object-fit: contain;">
                                <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*">
                                <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" onclick="$('#logoInput').click()">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Business Name</label>
                                <input type="text" name="name" class="form-control rounded-3" value="{{ $business->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Contact Email</label>
                                <input type="email" name="email" class="form-control rounded-3" value="{{ $business->email }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control rounded-3" value="{{ $business->mobile }}">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Currency</label>
                            <input type="text" name="currency" class="form-control rounded-3" value="{{ $business->currency ?? 'USD' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control rounded-3" value="{{ $business->currency_symbol ?? '$' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-muted">Address</label>
                            <textarea name="address" class="form-control rounded-3" rows="3">{{ $business->address }}</textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"
                            title="{{ auth()->user()->role === 'admin' ? 'Save Changes' : 'You do not have permission to perform this action' }}">
                            Save Changes
                        </button>
                    </div>
                </form>
            </x-widget>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#logoInput').on('change', function() {
            const reader = new FileReader();
            reader.onload = (e) => $('#logoPreview').attr('src', e.target.result);
            reader.readAsDataURL(this.files[0]);
        });

        $('#businessSettingsForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ route('business.update') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.msg);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        toastr.error('You need permission to perform this action.');
                    } else {
                        toastr.error('Error updating settings');
                    }
                }
            });
        });
    });
</script>
@endpush