<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no ?? $order->id }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px;
            color: #334155;
            background: #fff;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h2 {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-info p {
            margin: 5px 0 0;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 5px;
        }

        .info-p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th {
            background: #f8fafc;
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #f1f5f9;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }

        .total-row.grand-total {
            border-top: 2px solid #0f172a;
            margin-top: 10px;
            font-weight: 800;
            font-size: 18px;
            color: #0f172a;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                @if(isset($business->logo_url))
                <img src="{{ $business->logo_url }}" style="height: 40px;">
                @else
                {{ config('app.name', 'Furniture Admin') }}
                @endif
            </div>
            <div class="invoice-info">
                <h2>Invoice No</h2>
                <p>{{ $order->invoice_no ?? 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="details">
            <div>
                <div class="section-title">Billed To</div>
                <p class="info-p"><strong>{{ $order->user->username ?? 'Customer' }}</strong></p>
                <p class="info-p">{{ $order->user->email ?? '' }}</p>
                <p class="info-p">{{ $order->phone_number ?? '' }}</p>
            </div>
            <div>
                <div class="section-title">Shipping Address</div>
                <p class="info-p">{{ $order->shipping_address ?? 'No address provided' }}</p>
                <div class="section-title" style="margin-top: 15px;">Order Date</div>
                <p class="info-p">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Product Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product ? $item->product->name : 'Removed Product' }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($order->total_price - $order->shipping_charged, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Shipping</span>
                <span>${{ number_format($order->shipping_charged, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Total Amount</span>
                <span>${{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>{{ $business->name ?? '' }} | {{ $business->address ?? '' }}</p>
        </div>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #1e293b; color: #fff; border: none; border-radius: 5px;">Print Invoice</button>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>