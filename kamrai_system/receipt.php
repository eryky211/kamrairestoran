<?php
// Get the order ID from the URL (e.g., receipt.php?order_id=12)
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id === 0) {
    die("Error: No Order ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Order #<?php echo $order_id; ?></title>
    <!-- We link to the main style.css but will add print-specific styles -->
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Print-specific styles */
        @media print {
            body {
                font-family: 'Courier New', Courier, monospace;
                font-size: 12pt;
                color: #000;
                background-color: #fff;
            }
            .receipt-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .receipt-header {
                text-align: center;
            }
            .receipt-header h1 {
                font-size: 1.5rem;
                margin: 0;
            }
            .receipt-header p {
                font-size: 0.9rem;
                margin: 2px 0;
            }
            .order-details, .order-items, .order-total {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            .order-details td {
                padding: 3px 0;
            }
            .order-items th, .order-items td {
                padding: 5px 0;
                border-bottom: 1px dashed #999;
                text-align: left;
            }
            .order-items th:nth-child(2), .order-items td:nth-child(2) {
                text-align: center;
            }
            .order-items th:last-child, .order-items td:last-child {
                text-align: right;
            }
            .order-total td {
                padding: 5px 0;
                font-weight: bold;
                font-size: 1.1rem;
            }
            .order-total td:last-child {
                text-align: right;
            }
            .receipt-footer {
                text-align: center;
                margin-top: 20px;
                font-style: italic;
            }
            /* Hide the print button when printing */
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body class="receipt-body">

    <div class="receipt-container">
        <header class="receipt-header">
            <h1>Kamrai Restaurant</h1>
            <p>123 Restaurant Address, City, State</p>
            <p>Phone: (012) 345-6789</p>
            <hr>
        </header>

        <section class="receipt-info">
            <table class="order-details">
                <tr>
                    <td>Order ID:</td>
                    <td id="receipt-order-id">#<?php echo $order_id; ?></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td id="receipt-date">Loading...</td>
                </tr>
                <tr>
                    <td>Table:</td>
                    <td id="receipt-table">Loading...</td>
                </tr>
                <tr>
                    <td>Served By:</td>
                    <td id="receipt-waiter">Loading...</td>
                </tr>
            </table>
        </section>

        <section class="receipt-items">
            <table class="order-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="receipt-items-tbody">
                    <!-- Items will be loaded here -->
                </tbody>
            </table>
        </section>

        <section class="receipt-total">
            <table class="order-total">
                <!-- Subtotal, Tax, etc. can be added here -->
                <tr>
                    <td>TOTAL</td>
                    <td id="receipt-total-price">MYR 0.00</td>
                </tr>
            </table>
        </section>

        <footer class="receipt-footer">
            <p>Thank you for dining with us!</p>
        </footer>
    </div>

    <button class="print-button" onclick="window.print()">Print Receipt</button>

    <script>
        // Get the order ID from PHP
        const orderId = <?php echo $order_id; ?>;

        // Function to fetch and populate receipt data
        async function loadReceipt() {
            try {
                const response = await fetch(`api/get_receipt_details.php?order_id=${orderId}`);
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                // --- Populate Info ---
                const orderTime = new Date(data.order.order_time);
                document.getElementById('receipt-date').textContent = orderTime.toLocaleString('en-MY');
                document.getElementById('receipt-table').textContent = data.order.table_number;
                document.getElementById('receipt-waiter').textContent = data.order.full_name;

                // --- Populate Items ---
                const tbody = document.getElementById('receipt-items-tbody');
                tbody.innerHTML = ''; // Clear
                
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    const itemTotal = (item.quantity * item.item_price_at_order).toFixed(2);
                    row.innerHTML = `
                        <td>${item.item_name}</td>
                        <td style="text-align: center;">${item.quantity}</td>
                        <td style="text-align: right;">${itemTotal}</td>
                    `;
                    tbody.appendChild(row);
                });

                // --- Populate Total ---
                const formattedTotal = parseFloat(data.order.total_price).toFixed(2);
                document.getElementById('receipt-total-price').textContent = `MYR ${formattedTotal}`;
                
                // Optional: Auto-print when the page loads
                // window.print();

            } catch (error) {
                console.error('Error loading receipt data:', error);
                document.body.innerHTML = `<p>Error: Could not load receipt. ${error.message}</p>`;
            }
        }

        // Load data when page opens
        document.addEventListener('DOMContentLoaded', loadReceipt);
    </script>
</body>
</html>
