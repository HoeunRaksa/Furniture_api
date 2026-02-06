@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <x-widget title="Sales & Orders">
        <div class="table-responsive">
            <table class="table table-hover" id="ordersTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Invoice</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-widget>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Invoice #<span id="orderInvoiceNo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold mb-3">Customer & Shipping</h6>
                        <div id="customerInfoContainer"></div>
                        <p class="mb-1 text-muted" id="customerPhone"></p>
                        <p class="mb-0 small text-slate-600"><i class="bi bi-geo-alt me-1"></i><span id="shippingAddress"></span></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted small fw-bold">Order Details</h6>
                        <p class="mb-1" id="orderDate"></p>
                        <div class="mb-1">
                            <span class="badge bg-light text-dark border" id="paymentMethod"></span>
                        </div>
                        <div class="d-flex flex-column align-items-md-end gap-1">
                            <div id="paymentStatusBadge"></div>
                            <div id="shippingStatusBadge"></div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-sm table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2">Product</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-end py-2">Price</th>
                                <th class="text-end pe-3 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsList"></tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="3" class="text-end pt-3">Shipping Charge</th>
                                <th class="text-end pt-3 pe-3" id="shippingCharge"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end fw-bold fs-5">Total</th>
                                <th class="text-end pe-3 fw-bold fs-5 text-primary" id="orderTotalPrice"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="#" id="modalPrintBtn" target="_blank" class="btn btn-dark"><i class="bi bi-printer me-2"></i>Print Invoice</a>
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
            dom: '<"d-flex justify-content-between mb-2 px-1"lfB>rtip',
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-sm btn-light border'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-light border'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-light border'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-light border'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-light border'
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-sm btn-light border'
                }
            ],
            columns: [{
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'user_name',
                    name: 'user.username'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
                [4, 'desc']
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).addClass('cursor-pointer order-row').attr('data-id', data.id);
            }
        });

        // Row Click: View Order
        $(document).on('click', '.order-row td:not(:last-child)', function() {
            const id = $(this).closest('tr').data('id');
            openOrderDetails(id);
        });

        // Action Click: View Order
        $(document).on('click', '.view-order', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            openOrderDetails(id);
        });

        function openOrderDetails(id) {
            $.get(`/orders/show/${id}`, function(order) {
                $('#orderInvoiceNo').text(order.invoice_no || order.id);
                $('#customerName').text(order.user ? order.user.username : 'N/A');

                // User Image
                const userImage = order.user && order.user.profile_image ?
                    `{{ asset('') }}${order.user.profile_image}` :
                    `{{ asset('images/default-avatar.png') }}`;

                const userImageHtml = `
                    <div class="d-flex align-items-center mb-3">
                        <img src="${userImage}" class="rounded-circle border shadow-sm me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <p class="mb-0 fw-bold" id="customerName">${order.user ? order.user.username : 'N/A'}</p>
                            <small class="text-muted">${order.user ? order.user.email : 'N/A'}</small>
                        </div>
                    </div>
                `;
                $('#customerInfoContainer').html(userImageHtml);

                $('#customerPhone').text(order.phone_number || 'N/P');
                $('#shippingAddress').text(order.shipping_address || 'No address provided');
                $('#orderDate').text(new Date(order.created_at).toLocaleString());

                $('#paymentMethod').text(order.method || 'Cash');

                const statusBadges = {
                    'pending': 'bg-warning text-dark',
                    'paid': 'bg-success',
                    'shipped': 'bg-info',
                    'delivered': 'bg-primary',
                    'cancelled': 'bg-danger'
                };

                $('#paymentStatusBadge').html(`<span class="badge ${statusBadges[order.payment_status] || 'bg-secondary'}">Payment: ${order.payment_status}</span>`);
                $('#shippingStatusBadge').html(`<span class="badge ${statusBadges[order.shipping_status] || 'bg-secondary'}">Shipping: ${order.shipping_status}</span>`);

                let itemsHtml = '';
                order.items.forEach(item => {
                    itemsHtml += `
                            <tr>
                                <td class="ps-3">${item.product ? item.product.name : 'Removed Product'}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">$${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-end pe-3">$${(item.quantity * item.price).toFixed(2)}</td>
                            </tr>
                        `;
                });
                $('#orderItemsList').html(itemsHtml);
                $('#shippingCharge').text('$' + parseFloat(order.shipping_charged || 0).toFixed(2));
                $('#orderTotalPrice').text('$' + parseFloat(order.total_price).toFixed(2));

                // Set Print Button URL
                $('#modalPrintBtn').attr('href', `/orders/print/${order.id}`);

                $('#viewOrderModal').modal('show');
            });
        }

        // View on Map
        $(document).on('click', '.view-map', function(e) {
            e.preventDefault();
            const lat = $(this).data('lat');
            const long = $(this).data('long');
            if (lat && long) {
                window.open(`https://www.openstreetmap.org/?mlat=${lat}&mlon=${long}#map=18/${lat}/${long}`, '_blank');
            } else {
                toastr.warning('Location coordinates not available for this order.');
            }
        });

        // Delete Order
        $(document).on('click', '.delete-order', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            showConfirmModal('Are you sure you want to delete this order?', () => {
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
                        } else {
                            toastr.error(res.msg);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            toastr.error('You need permission to perform this action.');
                        } else {
                            const msg = xhr.responseJSON?.msg || 'Error deleting order';
                            toastr.error(msg);
                        }
                    }
                });
            });
        });
    });
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .order-row:hover {
        background-color: rgba(0, 0, 0, 0.015);
        transition: background 0.2s ease;
    }

    .no-caret::after {
        display: none;
    }

    .dropdown-item i {
        width: 1.2rem;
    }

    .dt-buttons .btn {
        margin-right: 5px;
    }
</style>
@endpush