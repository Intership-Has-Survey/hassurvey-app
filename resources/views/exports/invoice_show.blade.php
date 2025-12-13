<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eaeaea;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-number {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #3498db;
        }

        .invoice-meta {
            color: #7f8c8d;
            font-size: 14px;
        }

        .invoice-meta span {
            margin-left: 15px;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .bill-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }

        .bill-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .bill-details {
            line-height: 1.8;
        }

        .bill-details strong {
            color: #34495e;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            margin: 30px 0 20px 0;
            color: #2c3e50;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .items-table th {
            background-color: #3498db;
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 600;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table tr:hover {
            background-color: #f1f9ff;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }

        .total-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 18px;
            color: #2c3e50;
            padding-top: 15px;
        }

        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eaeaea;
        }

        .signature-box {
            display: inline-block;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 6px;
            min-width: 300px;
        }

        .notes-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 6px;
        }

        .notes-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eaeaea;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-print {
            background-color: #2ecc71;
            color: white;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-draft {
            background-color: #f39c12;
            color: white;
        }

        .status-pending {
            background-color: #3498db;
            color: white;
        }

        .status-paid {
            background-color: #2ecc71;
            color: white;
        }

        .status-overdue {
            background-color: #e74c3c;
            color: white;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .header {
                flex-direction: column;
            }

            .invoice-info {
                text-align: left;
                margin-top: 15px;
            }

            .container {
                padding: 15px;
            }

            .items-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1 class="invoice-title">View Invoice</h1>
            </div>
            <div class="invoice-info">
                <div class="invoice-number">Invoice #{{ $invoice->invoice_number }}</div>
                <div class="invoice-meta">
                    <span><i class="far fa-calendar"></i> Issue Date: {{ $invoice->issue_date }}</span>
                    <span><i class="far fa-calendar-check"></i> Due Date: {{ $invoice->due_date }}</span>
                    <span>
                        <i class="fas fa-circle status-{{ $invoice->status }}"></i>
                        Status: <span
                            class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                    </span>
                    <span><i class="fas fa-file-invoice"></i> Type: {{ $invoice->type }}</span>
                </div>
            </div>
        </div>

        <div class="two-column">
            <div class="bill-section">
                <h3 class="bill-title">Bill From</h3>
                <div class="bill-details">
                    <p><strong>{{ $invoice->bill_from_name }}</strong></p>
                    <p>{{ $invoice->bill_from_phone }}</p>
                </div>
            </div>

            <div class="bill-section">
                <h3 class="bill-title">Bill To</h3>
                <div class="bill-details">
                    <p><strong>{{ $invoice->bill_to_name }}</strong></p>
                    <p>{{ $invoice->bill_to_email }}</p>
                    <p>{{ $invoice->bill_to_phone }}</p>
                </div>
            </div>
        </div>

        <h2 class="section-title">Items</h2>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>QTY</th>
                    <th>Unit Price</th>
                    <th>VAT Rate</th>
                    <th>Discount Rate</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    @php
                        $itemSubtotal = $item->quantity * $item->unit_price;
                        $itemDiscount = $itemSubtotal * ($item->discount_rate / 100);
                        $itemAfterDiscount = $itemSubtotal - $itemDiscount;
                        $itemVat = $itemAfterDiscount * ($item->vat_rate / 100);
                        $itemTotal = $itemAfterDiscount + $itemVat;
                    @endphp
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->vat_rate }}%</td>
                        <td>{{ $item->discount_rate }}%</td>
                        <td><strong>${{ number_format($itemTotal, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($totals['subtotal'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>VAT Total:</span>
                <span>${{ number_format($totals['vat_total'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>Discount Total:</span>
                <span>${{ number_format($totals['discount_total'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>Grand Total:</span>
                <span>${{ number_format($totals['grand_total'], 2) }}</span>
            </div>
        </div>

        <div class="signature-section">
            <h3 class="bill-title">Signature</h3>
            <div class="signature-box">
                <p><strong>{{ $invoice->bill_from_name }}</strong></p>
                <p>{{ $invoice->bill_to_email }}</p>
                <p>{{ $invoice->bill_from_phone }}</p>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="notes-section">
                <h3 class="notes-title">Notes</h3>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <div class="action-buttons">
            <div>
                <a href="{{ route('invoices.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
                </a>
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete"
                        onclick="return confirm('Are you sure you want to delete this invoice?')">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            </div>
            <div>
                <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn btn-print">
                    <i class="fas fa-print"></i> Print Invoice
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Invoice generated on {{ date('Y-m-d') }} | Invoice ID: {{ $invoice->id }}</p>
        </div>
    </div>

    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

    @if (session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif
</body>

</html>
