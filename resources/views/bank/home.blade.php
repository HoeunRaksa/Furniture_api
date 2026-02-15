<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank of Simulation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 60px 0; border-bottom-left-radius: 50% 20px; border-bottom-right-radius: 50% 20px;}
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .btn-custom { background-color: #0d6efd; color: white; padding: 12px 30px; border-radius: 30px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s; }
        .btn-custom:hover { background-color: #0b5ed7; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); color: white; }
        .invoice-input { border-radius: 10px; padding: 12px; border: 1px solid #ced4da; background-color: #f8f9fa; }
        .invoice-input:focus { box-shadow: none; border-color: #0d6efd; background-color: white; }
        #reader { width: 100%; border-radius: 15px; overflow: hidden; margin-bottom: 20px; display: none; }
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
                        <p class="text-muted small">Scan QR or enter invoice number.</p>
                    </div>

                    <div id="reader"></div>
                    <button type="button" class="btn btn-outline-primary w-100 mb-3" onclick="startScanner()">
                        📷 Scan QR Code
                    </button>

                    <form id="payForm" onsubmit="submitInvoice(event)">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Invoice Number</label>
                            <input type="text" id="invoiceInput" class="form-control invoice-input" placeholder="INV-XXXXXXXXXXXXX" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Proceed to Pay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Handle the scanned code as you like, for example:
            console.log(`Code matched = ${decodedText}`, decodedResult);
            
            // Should match: https://bank.furniture.../pay/INV-XXXX
            // Or just: INV-XXXX
            let invoice = decodedText;
            
            // If full URL, extract invoice
            if (decodedText.includes('/pay/')) {
                const parts = decodedText.split('/pay/');
                if (parts.length > 1) {
                    invoice = parts[1];
                }
            }

            document.getElementById('invoiceInput').value = invoice;
            
            // Stop scanning
            html5QrcodeScanner.clear();
            document.getElementById('reader').style.display = 'none';

            // Auto submit
            window.location.href = '/pay/' + invoice;
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner;

        function startScanner() {
            document.getElementById('reader').style.display = 'block';
            
            // Allow camera selection and improve scanning for mobile
             html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10, 
                    qrbox: {width: 250, height: 250},
                    aspectRatio: 1.0,
                    showTorchButtonIfSupported: true
                },
                /* verbose= */ false);
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        // Check query params
        const urlParams = new URLSearchParams(window.location.search);
        const invoiceParam = urlParams.get('invoice');
        
        if (invoiceParam) {
            document.getElementById('invoiceInput').value = invoiceParam;
        }

        function submitInvoice(e) {
            e.preventDefault();
            const invoice = document.getElementById('invoiceInput').value.trim();
            if (invoice) {
                window.location.href = '/pay/' + invoice;
            }
        }
    </script>
</body>
</html>
