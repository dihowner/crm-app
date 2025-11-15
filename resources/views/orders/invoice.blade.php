<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - CRM App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .company-info h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }
        .company-info p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 1.1rem;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .invoice-details p {
            margin: 5px 0;
            color: #6c757d;
        }
        .customer-details {
            margin-bottom: 40px;
        }
        .customer-details h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: 600;
            color: #333;
        }
        .details-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            color: #666;
        }
        .order-items {
            margin-bottom: 40px;
        }
        .order-items h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: 600;
            color: #333;
        }
        .items-table td {
            padding: 15px;
            border: 1px solid #dee2e6;
            color: #666;
        }
        .total-section {
            text-align: right;
            margin-top: 30px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 200px;
            text-align: right;
            padding-right: 20px;
            font-weight: 600;
            color: #333;
        }
        .total-value {
            width: 150px;
            text-align: right;
            color: #666;
        }
        .grand-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
            border-top: 2px solid #e9ecef;
            padding-top: 10px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-new { background-color: #cfe2ff; color: #0a58ca; }
        .status-scheduled { background-color: #fff3cd; color: #997404; }
        .status-delivered { background-color: #d1e7dd; color: #0f5132; }
        .status-cancelled { background-color: #f8d7da; color: #842029; }
        .status-failed { background-color: #f8d7da; color: #842029; }
        .status-paid { background-color: #d1e7dd; color: #0f5132; }
        @media print {
            body { background-color: white; }
            .invoice-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ \App\Models\AppSetting::getValue('app_name', config('app.name', 'CRM System')) }}</h1>
                <p>Professional Order Management System</p>
            </div>
            <div class="invoice-details">
                <h2>Invoice #{{ $order->order_number }}</h2>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="customer-details">
            <h3>Customer Details</h3>
            <table class="details-table">
                <tr>
                    <th>Name</th>
                    <td>{{ $order->customer->name }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $order->customer->phone }}</td>
                </tr>
                @if($order->customer->whatsapp_number)
                <tr>
                    <th>WhatsApp</th>
                    <td>{{ $order->customer->whatsapp_number }}</td>
                </tr>
                @endif
                <tr>
                    <th>Address</th>
                    <td>{{ $order->customer->address }}, {{ $order->customer->state }}</td>
                </tr>
            </table>
        </div>

        <!-- Order Items -->
        <div class="order-items">
            <h3>Order Details</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $order->product->name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>₦{{ number_format($order->unit_price, 2) }}</td>
                        <td>₦{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">₦{{ number_format($order->total_price, 2) }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Tax (0%):</div>
                <div class="total-value">₦0.00</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">Total Amount:</div>
                <div class="total-value">₦{{ number_format($order->total_price, 2) }}</div>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top: 40px;">
            <h3>Notes</h3>
            <p style="color: #666; font-style: italic;">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing {{ \App\Models\AppSetting::getValue('app_name', config('app.name', 'CRM System')) }}. We value your patronage!</p>
            <p><small>This is an automated invoice generated on {{ now()->format('M d, Y H:i') }}</small></p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
