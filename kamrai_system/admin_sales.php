<?php 
require_once 'auth.php'; 
check_role('admin'); // Only admins can see sales
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sales Report</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">

        <header>
            <h1>Sales Report</h1>
        </header>

        <main>
            <h2>Completed Orders (Paid)</h2>
            <div class="sales-summary">
                <strong>Total Sales Today: </strong>
                <span id="total-sales">MYR 0.00</span>
            </div>

            <table class="styled-table" id="sales-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Table</th>
                        <th>Waiter/Admin</th>
                        <th>Order Time</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody id="sales-tbody">
                    <!-- Sales data will be loaded here by JavaScript -->
                    <tr><td colspan="5">Loading sales data...</td></tr>
                </tbody>
            </table>
        </main>
    
    </div> <!-- End .main-content -->

    <script>
        // Function to fetch and display sales report
        async function fetchSalesReport() {
            try {
                const response = await fetch('api/get_sales_report.php');
                const data = await response.json();

                const tbody = document.getElementById('sales-tbody');
                const totalSalesEl = document.getElementById('total-sales');
                tbody.innerHTML = ''; // Clear 'Loading...'

                if (data.orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5">No paid orders found.</td></tr>';
                    return;
                }

                // Update total sales display
                totalSalesEl.textContent = `MYR ${parseFloat(data.total_sales_today).toFixed(2)}`;

                // Populate the table
                data.orders.forEach(order => {
                    const row = document.createElement('tr');
                    
                    const formattedPrice = parseFloat(order.total_price).toFixed(2);
                    
                    // Format the timestamp
                    const orderTime = new Date(order.order_time);
                    const formattedDateTime = orderTime.toLocaleString('en-MY', {
                        day: '2-digit',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    row.innerHTML = `
                        <td>${order.order_id}</td>
                        <td>${order.table_number}</td>
                        <td>${order.full_name}</td>
                        <td>${formattedDateTime}</td>
                        <td><strong>MYR ${formattedPrice}</strong></td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error('Error fetching sales:', error);
                document.getElementById('sales-tbody').innerHTML = '<tr><td colspan="5">Error loading sales data.</td></tr>';
            }
        }

        // Load orders when page opens
        document.addEventListener('DOMContentLoaded', fetchSalesReport);
    </script>
</body>
</html>

