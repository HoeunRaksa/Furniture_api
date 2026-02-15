<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank of Simulation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 60px 0; border-bottom-left-radius: 50% 20px; border-bottom-right-radius: 50% 20px;}
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .btn-custom { background-color: #0d6efd; color: white; padding: 12px 30px; border-radius: 30px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s; }
        .btn-custom:hover { background-color: #0b5ed7; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); color: white; }
        .invoice-input { border-radius: 10px; padding: 12px; border: 1px solid #ced4da; background-color: #f8f9fa; }
        .invoice-input:focus { box-shadow: none; border-color: #0d6efd; background-color: white; }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <div class="hero text-center mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Bank of Simulation</h1>
            <p class="lead opacity-75">Secure, Fast, and Reliable Payments for Your Furniture.</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-5">
                    <div class="text-center mb-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/2830/2830284.png" alt="Bank Logo" width="80" class="mb-3">
                        <h3 class="fw-bold text-dark">Pay an Invoice</h3>
                        <p class="text-muted small">Enter your invoice number to proceed with payment.</p>
                    </div>

                    <form id="payForm" onsubmit="submitInvoice(event)">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Invoice Number</label>
                            <input type="text" id="invoiceInput" class="form-control invoice-input" placeholder="INV-XXXXXXXXXXXXX" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Proceed to Pay</button>
                    </form>
                </div>

                <div class="text-center mt-5">
                    <p class="text-muted small">&copy; 2026 Bank of Simulation. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check if invoice is in URL query params (e.g. ?invoice=INV-123)
        const urlParams = new URLSearchParams(window.location.search);
        const invoiceParam = urlParams.get('invoice');
        
        if (invoiceParam) {
            document.getElementById('invoiceInput').value = invoiceParam;
            // Optional: Auto-submit if present?
            // submitInvoice({preventDefault: () => {}}); 
        }

        function submitInvoice(e) {
            e.preventDefault();
            const invoice = document.getElementById('invoiceInput').value.trim();
            if (invoice) {
                // Redirect to the payment route we already built
                window.location.href = '/pay/' + invoice;
            }
        }
    </script>
</body>
</html>
