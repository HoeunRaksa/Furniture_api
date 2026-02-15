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

                    <form action="https://api.furniture.learner-teach.online/pay/{{ $order->invoice_no }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="123456789" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="******" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Confirm Payment</button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <small class="text-muted">Secured by Simulation Bank</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
