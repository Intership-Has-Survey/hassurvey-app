<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management System</title>
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
                <h1 class="invoice-title">View Invoices</h1>
            </div>
            <div class="invoice-info">
                <div class="invoice-number">Invoice #INV-2024-001</div>
                <div class="invoice-meta">
                    <span><i class="far fa-calendar"></i> Issue Date: 2024-08-20</span>
                    <span><i class="far fa-calendar-check"></i> Due Date: 2024-02-04</span>
                    <span><i class="fas fa-circle status-draft status-badge"></i> Status: <span
                            class="status-badge status-draft">Draft</span></span>
                    <span><i class="fas fa-file-invoice"></i> Type: Push Invoice</span>
                </div>
            </div>
        </div>

        <div class="two-column">
            <div class="bill-section">
                <h3 class="bill-title">Bill From</h3>
                <div class="bill-details">
                    <p><strong>Madisyn Hamill</strong></p>
                    <p>995-605-8039</p>
                </div>
            </div>

            <div class="bill-section">
                <h3 class="bill-title">Bill To</h3>
                <div class="bill-details">
                    <p><strong>Allan Dietrich</strong></p>
                    <p>youremail+takedat315183@gmail.com</p>
                    <p>484-561-7870</p>
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
                    <th>VAT</th>
                    <th>Discount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="invoice-items">
                <!-- Items will be loaded from database here -->
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span id="subtotal">0.00 USD</span>
            </div>
            <div class="total-row">
                <span>VAT (20%):</span>
                <span id="vat-total">0.00 USD</span>
            </div>
            <div class="total-row">
                <span>Discount:</span>
                <span id="discount-total">0.00 USD</span>
            </div>
            <div class="total-row">
                <span>Grand Total:</span>
                <span id="grand-total">0.00 USD</span>
            </div>
        </div>

        <div class="signature-section">
            <h3 class="bill-title">Signature</h3>
            <div class="signature-box">
                <p><strong>Madisyn Hamill</strong></p>
                <p>youremail+takedat38157@gmail.com</p>
                <p>995-605-8039</p>
            </div>
        </div>

        <div class="notes-section">
            <h3 class="notes-title">Notes</h3>
            <p>Payment due within 30 days of invoice date. Please include invoice number in your payment reference.</p>
        </div>

        <div class="action-buttons">
            <div>
                <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-delete"><i class="fas fa-trash-alt"></i> Delete</button>
            </div>
            <div>
                <button class="btn btn-print"><i class="fas fa-print"></i> Print Invoice</button>
            </div>
        </div>

        <div class="footer">
            <p>Invoice generated on 2024-08-20 | This is a system generated invoice</p>
        </div>
    </div>

    <script>
        // Sample data - in a real application, this would come from a database
        const invoiceItems = [{
                id: 1,
                description: "Web Development Services",
                qty: 40,
                unitPrice: 85.50,
                vat: 20,
                discount: 0
            },
            {
                id: 2,
                description: "UI/UX Design",
                qty: 25,
                unitPrice: 65.00,
                vat: 20,
                discount: 5
            },
            {
                id: 3,
                description: "Consulting Hours",
                qty: 15,
                unitPrice: 120.00,
                vat: 20,
                discount: 0
            },
            {
                id: 4,
                description: "Hosting Services (Annual)",
                qty: 1,
                unitPrice: 450.00,
                vat: 20,
                discount: 10
            },
            {
                id: 5,
                description: "SEO Optimization Package",
                qty: 1,
                unitPrice: 1200.00,
                vat: 20,
                discount: 15
            }
        ];

        // Function to format currency
        function formatCurrency(amount) {
            return amount.toFixed(2) + " USD";
        }

        // Function to calculate item totals
        function calculateItemTotal(item) {
            const subtotal = item.qty * item.unitPrice;
            const discountAmount = subtotal * (item.discount / 100);
            const amountAfterDiscount = subtotal - discountAmount;
            const vatAmount = amountAfterDiscount * (item.vat / 100);
            return {
                subtotal: subtotal,
                discountAmount: discountAmount,
                vatAmount: vatAmount,
                total: amountAfterDiscount + vatAmount
            };
        }

        // Function to render invoice items
        function renderInvoiceItems() {
            const itemsContainer = document.getElementById('invoice-items');
            let subtotal = 0;
            let vatTotal = 0;
            let discountTotal = 0;
            let grandTotal = 0;

            // Clear existing items
            itemsContainer.innerHTML = '';

            // Add each item to the table
            invoiceItems.forEach(item => {
                const itemTotals = calculateItemTotal(item);

                subtotal += itemTotals.subtotal;
                vatTotal += itemTotals.vatAmount;
                discountTotal += itemTotals.discountAmount;
                grandTotal += itemTotals.total;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.description}</td>
                    <td>${item.qty}</td>
                    <td>${formatCurrency(item.unitPrice)}</td>
                    <td>${item.vat}% (${formatCurrency(itemTotals.vatAmount)})</td>
                    <td>${item.discount}% (${formatCurrency(itemTotals.discountAmount)})</td>
                    <td><strong>${formatCurrency(itemTotals.total)}</strong></td>
                `;
                itemsContainer.appendChild(row);
            });

            // Update summary totals
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('vat-total').textContent = formatCurrency(vatTotal);
            document.getElementById('discount-total').textContent = formatCurrency(discountTotal);
            document.getElementById('grand-total').textContent = formatCurrency(grandTotal);
        }

        // Function to handle print button
        function setupPrintButton() {
            document.querySelector('.btn-print').addEventListener('click', function() {
                window.print();
            });
        }

        // Function to handle edit button
        function setupEditButton() {
            document.querySelector('.btn-edit').addEventListener('click', function() {
                alert('Edit functionality would open a form to modify invoice details.');
            });
        }

        // Function to handle delete button
        function setupDeleteButton() {
            document.querySelector('.btn-delete').addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this invoice?')) {
                    alert('Invoice deleted successfully (demo only).');
                }
            });
        }

        // Initialize the invoice when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            renderInvoiceItems();
            setupPrintButton();
            setupEditButton();
            setupDeleteButton();
        });
    </script>
</body>

</html>
