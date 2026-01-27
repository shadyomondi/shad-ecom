<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
            font-size: 28px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
        }
        .customer-details, .shipping-details {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #2563eb;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
            font-weight: bold;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .summary-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 8px 0;
        }
        .summary-table .total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>{{ config('app.name', 'E-Commerce Store') }}</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td><strong>Invoice Number:</strong> {{ $order->order_number }}</td>
                <td class="text-right"><strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Payment Status:</strong>
                    <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="customer-details">
        <div class="section-title">Customer Information</div>
        <p>
            <strong>Name:</strong> {{ $order->user->name }}<br>
            <strong>Email:</strong> {{ $order->user->email }}
        </p>
    </div>

    <div class="shipping-details">
        <div class="section-title">Shipping Address</div>
        <p>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_city }}, {{ $order->shipping_zip }}
        </p>
    </div>

    <div class="section-title">Order Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Price</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        @if($item->product)
                            {{ $item->product->name }}
                        @else
                            Product (Deleted)
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">${{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax (16%):</td>
            <td class="text-right">${{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr class="total">
            <td>Total:</td>
            <td class="text-right">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions about this invoice, please contact us.</p>
        <p>{{ config('app.name', 'E-Commerce Store') }} | Generated on {{ now()->format('F d, Y') }}</p>
    </div>
</body>
</html>
