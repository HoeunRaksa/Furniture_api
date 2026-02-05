@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Sales & Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="ordersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Order Details #<span id="orderIdHeader"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold">Customer Info</h6>
                            <p class="mb-1" id="customerName"></p>
                            <p class="mb-0 text-muted" id="customerEmail"></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted small text-uppercase fw-bold">Order Info</h6>
                            <p class="mb-1" id="orderDate"></p>
                            <p id="orderStatusBadge"></p>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsList"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-end" id="orderTotalPrice"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('orders.data') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'total_price', name: 'total_price' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'desc']]
            });

            $(document).on('click', '.view-order', function() {
                const id = $(this).data('id');
                $.get(`/orders/show/${id}`, function(order) {
                    $('#orderIdHeader').text(order.id);
                    $('#customerName').text(order.user ? order.user.name : 'N/A');
                    $('#customerEmail').text(order.user ? order.user.email : 'N/A');
                    $('#orderDate').text(new Date(order.created_at).toLocaleString());
                    
                    let statusBadge = `<span class="badge bg-secondary">${order.status}</span>`;
                    if (order.status === 'paid') statusBadge = `<span class="badge bg-success">Paid</span>`;
                    if (order.status === 'pending') statusBadge = `<span class="badge bg-warning text-dark">Pending</span>`;
                    $('#orderStatusBadge').html(statusBadge);

                    let itemsHtml = '';
                    order.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.product ? item.product.name : 'Removed Product'}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">$${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-end">$${(item.quantity * item.price).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    $('#orderItemsList').html(itemsHtml);
                    $('#orderTotalPrice').text('$' + parseFloat(order.total_price).toFixed(2));
                    
                    $('#viewOrderModal').modal('show');
                });
            });

            $(document).on('click', '.delete-order', function() {
                const url = $(this).data('url');
                if (confirm('Are you sure you want to delete this order?')) {
                    $.ajax({
                        url: url,
                        method: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.msg);
                                table.ajax.reload();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
