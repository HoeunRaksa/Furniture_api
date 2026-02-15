<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-primary { border-radius: 10px; padding: 12px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Bank Payment</h2>
                        <p class="text-muted">Transfer for Invoice: <span class="text-primary">{{ $order->invoice_no }}</span></p>
                    </div>

                    <div class="alert alert-info d-flex justify-content-between">
                        <span>Amount to Pay:</span>
                        <strong class="text-danger">${{ number_format($order->total_price, 2) }}</strong>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="paymentForm" action="{{ route('pay.process', $order->invoice_no) }}" method="POST">
                        @csrf
                        <!-- Hidden Pre-filled Credentials -->
                        <input type="hidden" name="account_number" value="123456789">
                        <input type="hidden" name="password" value="123456">

                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">${{ number_format($order->total_price, 2) }}</h2>
                            <p class="text-muted">Invoice: {{ $order->invoice_no }}</p>
                            <div class="spinner-border text-primary mt-3" role="status">
                                <span class="visually-hidden">Processing...</span>
                            </div>
                            <p class="text-muted mt-2">Processing payment automatically...</p>
                        </div>
                    </form>

                    <script>
                        // Auto-submit the form after 1 second
                        setTimeout(function() {
                            document.getElementById('paymentForm').submit();
                        }, 1000);
                    </script>
                    
                    <div class="mt-4 text-center">
                        <small class="text-muted">Secured by Simulation Bank</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
