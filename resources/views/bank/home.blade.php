<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Payment Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .scanner-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .logo svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #666;
            font-size: 15px;
        }

        #reader {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-scan {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 18px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-scan:active {
            transform: translateY(0);
        }

        .btn-scan svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .input-group {
            margin-top: 20px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-submit {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #667eea;
            color: white;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
            color: #999;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider span {
            padding: 0 16px;
        }

        .result-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .result-card strong {
            color: #1a1a1a;
            font-size: 16px;
        }

        .btn-confirm {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-top: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .security-badge {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
        }

        .security-badge svg {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            vertical-align: middle;
            fill: #11998e;
        }

        .security-badge span {
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <div class="header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                </svg>
            </div>
            <h1>Payment Scanner</h1>
            <p class="subtitle">Scan QR code or enter invoice number</p>
        </div>

        <div id="reader" style="display: none;"></div>
        
        <button type="button" class="btn-scan" onclick="startScanner()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 5v4h2V5h4V3H5c-1.1 0-2 .9-2 2zm2 10H3v4c0 1.1.9 2 2 2h4v-2H5v-4zm14 4h-4v2h4c1.1 0 2-.9 2-2v-4h-2v4zm0-16h-4v2h4v4h2V5c0-1.1-.9-2-2-2z"/>
            </svg>
            Start Camera Scanner
        </button>

        <div class="divider"><span>OR</span></div>

        <form action="/pay" method="GET" class="input-group">
            <input type="text" name="invoice" class="form-control" placeholder="Enter Invoice Number (e.g., INV-123456)" required>
            <button type="submit" class="btn-submit mt-3 w-100">Continue</button>
        </form>

        <div id="result" style="display: none;"></div>

        <div class="security-badge">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
            </svg>
            <span>Secured Payment Gateway</span>
        </div>
    </div>

    <script>
        let html5Qrcode;

        async function startScanner() {
            const readerElement = document.getElementById('reader');
            readerElement.style.display = 'block';
            document.getElementById('result').style.display = 'none';
            
            html5Qrcode = new Html5Qrcode("reader");
            
            try {
                // Start scanning with back camera only
                await html5Qrcode.start(
                    { facingMode: "environment" }, // Force back camera
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    onScanSuccess,
                    onScanFailure
                );
            } catch (err) {
                console.error("Camera error:", err);
                alert("Unable to access camera. Please allow camera permissions.");
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scanned: ${decodedText}`);
            
            // Stop scanning
            html5Qrcode.stop().then(() => {
                document.getElementById('reader').style.display = 'none';
            });

            let invoice = decodedText;
            
            if (decodedText.includes('/pay/')) {
                const parts = decodedText.split('/pay/');
                if (parts.length > 1) {
                    invoice = parts[1];
                }
            } else if (decodedText.startsWith('{')) {
                try {
                    const data = JSON.parse(decodedText);
                    invoice = data.invoice_no || data.invoice || decodedText;
                } catch (e) {
                    console.log('Not valid JSON, using raw text');
                }
            }

            document.getElementById('result').style.display = 'block';
            document.getElementById('result').className = 'result-card';
            document.getElementById('result').innerHTML = `
                <strong>✓ Invoice Detected</strong><br>
                <div style="font-size: 18px; font-weight: 600; color: #667eea; margin: 12px 0;">${invoice}</div>
                <button class="btn-confirm" onclick="processPayment('${invoice}')">
                    Confirm Payment →
                </button>
            `;
        }

        function onScanFailure(error) {
            // Ignore scan failures (normal when searching)
        }

        function processPayment(invoice) {
            window.location.href = `/pay/${invoice}`;
        }
    </script>
</body>
</html>
