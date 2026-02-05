/**
 * Notification Manager
 * Handles real-time order alerts and UI updates
 */

class NotificationManager {
    constructor() {
        this.lastCheck = null;
        this.pollInterval = 1000; // 1 second
        this.container = $('#notification-items');
        this.badge = $('#notification-badge');
        this.audio = document.getElementById('notification-audio');

        this.init();
    }

    init() {
        // Initial load of latest orders
        this.fetchNotifications();

        // Start polling
        setInterval(() => this.fetchNotifications(), this.pollInterval);
    }

    fetchNotifications() {
        $.ajax({
            url: '/notifications/check',
            method: 'GET',
            data: { since: this.lastCheck },
            success: (res) => {
                if (res.new_orders && res.new_orders.length > 0) {
                    this.handleNewOrders(res.new_orders);
                }

                if (res.latest_orders) {
                    this.updateDropdown(res.latest_orders);
                }

                this.lastCheck = res.timestamp;
            },
            error: (err) => console.error('Notification check failed:', err)
        });
    }

    handleNewOrders(orders) {
        orders.forEach(order => {
            // Play sound
            if (this.audio) {
                this.audio.play().catch(e => console.log('Audio play blocked by browser'));
            }

            // Show Toast
            toastr.success(
                `New Order: <b>${order.invoice_no}</b><br>Customer: ${order.customer}<br>Amount: ${order.amount}`,
                'New Order Received!',
                {
                    timeOut: 10000,
                    onclick: () => window.location.href = order.url
                }
            );
        });

        this.updateBadge(orders.length);
    }

    updateBadge(newCount) {
        let currentCount = parseInt(this.badge.text()) || 0;
        let total = currentCount + newCount;

        if (total > 0) {
            this.badge.text(total).removeClass('d-none').addClass('animate__animated animate__pulse');
        }
    }

    updateDropdown(orders) {
        if (!this.container.length) return;

        if (orders.length === 0) {
            this.container.html('<li><span class="dropdown-item text-muted small py-3 text-center">No recent orders</span></li>');
            return;
        }

        let html = '';
        orders.forEach(order => {
            html += `
                <li>
                    <a class="dropdown-item d-flex align-items-center py-3 border-bottom" href="${order.url}">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="bi bi-cart-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-bold text-dark">${order.invoice_no}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">${order.time}</span>
                            </div>
                            <div class="small text-muted text-truncate" style="max-width: 180px;">
                                ${order.customer} - ${order.amount}
                            </div>
                        </div>
                    </a>
                </li>
            `;
        });

        this.container.html(html);
    }
}

$(document).ready(() => {
    window.notificationManager = new NotificationManager();
});
