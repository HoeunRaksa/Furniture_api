@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="col-md-8">
                <x-widget title="Attribute Management">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createAttributeModal">
                            <i class="bi bi-plus-lg me-2"></i> Add Attribute
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="attributesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Attribute Name</th>
                                    <th>Values Count</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </x-widget>
            </div>

            <div id="valuesSection" class="col-md-4 d-none">
                <x-widget title="Values for: <span id='currentAttributeName' class='text-primary'></span>">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" onclick="showAddValueForm()">
                            <i class="bi bi-plus"></i> Add Value
                        </button>
                    </div>
                    <div id="addValueForm" class="d-none mb-4 p-3 bg-light rounded-3">
                        <form id="storeValueForm">
                            @csrf
                            <input type="hidden" name="attribute_id" id="currentAttributeId">
                            <div class="input-group">
                                <input type="text" name="value" class="form-control" placeholder="Value (e.g. Red, XL, Metal)" required>
                                <button class="btn btn-primary" type="submit">Add</button>
                            </div>
                        </form>
                    </div>
                    <ul class="list-group list-group-flush" id="valuesList"></ul>
                </x-widget>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAttributeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="createAttributeForm">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Add Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Attribute Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Color, Size, Material" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAttributeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="editAttributeForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Attribute Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#attributesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attributes.data') }}",
                dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'values_count',
                        name: 'values_count',
                        className: 'text-center'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            // Create
            $('#createAttributeForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('attributes.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $('#createAttributeForm')[0].reset();
                            $('#createAttributeModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            });

            // Edit Open
            $(document).on('click', '.edit-attribute', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#editAttributeModal').modal('show');
            });

            // Update
            $('#editAttributeForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_id').val();
                $.ajax({
                    url: `/attributes/update/${id}`,
                    method: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $('#editAttributeModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-attribute', function() {
                const url = $(this).data('url');
                showConfirmModal('Delete this attribute and all its values?', () => {
                    $.ajax({
                        url: url,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.msg);
                                table.ajax.reload();
                                if ($('#currentAttributeId').val() == url.split('/').pop()) {
                                    $('#valuesSection').addClass('d-none');
                                }
                            }
                        }
                    });
                });
            });

            // View Values
            $(document).on('click', '.view-values', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#currentAttributeId').val(id);
                $('#currentAttributeName').text(name);
                $('#valuesSection').removeClass('d-none');
                $('#addValueForm').addClass('d-none');
                loadValues(id);
            });

            // Store Value
            $('#storeValueForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('attributes.value.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            $('#storeValueForm')[0].reset();
                            $('#storeValueForm input[name="attribute_id"]').val($('#currentAttributeId').val());
                            loadValues($('#currentAttributeId').val());
                            table.ajax.reload(null, false);
                        }
                    }
                });
            });
        });

        function loadValues(attributeId) {
            $.get(`/attributes/values/${attributeId}`, function(values) {
                let html = '';
                values.forEach(v => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0">
                            <span>${v.value}</span>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteValue(${v.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </li>
                    `;
                });
                if (values.length === 0) {
                    html = '<li class="list-group-item text-muted small border-0 px-0">No values yet</li>';
                }
                $('#valuesList').html(html);
            });
        }

        function showAddValueForm() {
            $('#addValueForm').toggleClass('d-none');
        }

        function deleteValue(id) {
            showConfirmModal('Delete this value?', () => {
                $.ajax({
                    url: `/attributes/value/destroy/${id}`,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg);
                            loadValues($('#currentAttributeId').val());
                            $('#attributesTable').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        }
    </script>
    @endpush