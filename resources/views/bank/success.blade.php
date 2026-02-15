<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .success-icon { font-size: 80px; color: #198754; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-5 text-center">
                    <div class="success-icon mb-4">✔</div>
                    <h2 class="fw-bold text-success">Payment Successful!</h2>
                    <p class="text-muted mt-3">Thank you for your purchase.</p>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Invoice:</span>
                        <strong>{{ $order->invoice_no }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Status:</span>
                        <span class="badge bg-success">PAID</span>
                    </div>
                    <p class="small text-muted">You can close this window now. Your app will update automatically.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
