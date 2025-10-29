<?php 
require_once 'auth.php'; 
check_role(['cashier', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">

        <header>
            <h1>Cashier Dashboard</h1>
        </header>

        <main>
            <h2>Open Bills (Unpaid Orders)</h2>
            <table class="styled-table" id="unpaid-orders-table">
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Order ID</th>
                        <th>Order Time</th>
                        <th>Total Price</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    <!-- Unpaid orders will be loaded here -->
                    <tr><td colspan="6">Loading...</td></tr>
                </tbody>
            </table>
        </main>
    
    </div> <!-- End .main-content -->

    <!-- === NEW: Payment Modal === -->
    <div id="payment-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closePaymentModal()">&times;</span>
            <h2>Process Payment</h2>
            <h3>Order #<span id="modal-order-id"></span></h3>
            
            <div class="modal-form">
                <div class="payment-total">
                    <span>Total Amount Due:</span>
                    <strong>MYR <span id="modal-total-price">0.00</span></strong>
                </div>

                <hr class="form-divider">

                <div class="payment-calculator">
                    <label for="amount-received">Amount Received (Cash):</label>
                    <input type="number" id="amount-received" placeholder="e.g., 50.00">
                    <button id="calc-change-btn">Calculate Change</button>
                    <div class="payment-change">
                        <span>Change Due:</span>
                        <strong>MYR <span id="modal-change-due">0.00</span></strong>
                    </div>
                </div>

                <hr class="form-divider">

                <div class="payment-buttons">
                    <button class="btn-pay" id="modal-pay-cash-btn">Mark as Paid (Cash)</button>
                    <button class="btn-edit" id="modal-pay-card-btn" style="background-color: #3498db;">Mark as Paid (Card)</button>
                </div>
            </div>
        </div>
    </div>
    <!-- === End Payment Modal === -->

    <script>
        // Store current order details for the modal
        let currentModalData = {
            orderId: 0,
            tableId: 0,
            totalPrice: 0.00
        };

        // --- 1. Function to fetch and display unpaid orders ---
        async function fetchUnpaidOrders() {
            try {
                const response = await fetch('api/get_unpaid_orders.php');
                const orders = await response.json();

                const tbody = document.getElementById('orders-tbody');
                tbody.innerHTML = ''; // Clear 'Loading...'

                if (orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6">No unpaid orders found.</td></tr>';
                    return;
                }

                orders.forEach(order => {
                    const row = document.createElement('tr');
                    row.id = `order-row-${order.order_id}`;
                    const formattedPrice = parseFloat(order.total_price).toFixed(2);
                    const orderTime = new Date(order.order_time);
                    const formattedTime = orderTime.toLocaleTimeString('en-US', {
                        hour: '2-digit', minute: '2-digit'
                    });

                    // Store data in a string for the onclick function
                    const orderData = JSON.stringify({
                        orderId: order.order_id,
                        tableId: order.table_id,
                        totalPrice: formattedPrice
                    });

                    row.innerHTML = `
                        <td><strong>${order.table_number}</strong></td>
                        <td>${order.order_id}</td>
                        <td>${formattedTime}</td>
                        <td><strong>MYR ${formattedPrice}</strong></td>
                        <td><span class="status-badge ${order.order_status.toLowerCase()}">${order.order_status}</span></td>
                        <td>
                            <!-- Updated button to open modal -->
                            <button class="btn-pay" onclick='openPaymentModal(${orderData})'>
                                Process Payment
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error('Error fetching orders:', error);
                document.getElementById('orders-tbody').innerHTML = '<tr><td colspan="6">Error loading orders.</td></tr>';
            }
        }

        // --- 2. NEW: Functions to control the payment modal ---
        function openPaymentModal(orderData) {
            // Save data
            currentModalData.orderId = orderData.orderId;
            currentModalData.tableId = orderData.tableId;
            currentModalData.totalPrice = parseFloat(orderData.totalPrice);

            // Populate modal fields
            document.getElementById('modal-order-id').textContent = currentModalData.orderId;
            document.getElementById('modal-total-price').textContent = currentModalData.totalPrice.toFixed(2);
            document.getElementById('amount-received').value = '';
            document.getElementById('modal-change-due').textContent = '0.00';

            // Show modal
            document.getElementById('payment-modal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').style.display = 'none';
        }

        // --- 3. NEW: Function to calculate change ---
        document.getElementById('calc-change-btn').addEventListener('click', () => {
            const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
            const changeDue = amountReceived - currentModalData.totalPrice;
            
            if (changeDue >= 0) {
                document.getElementById('modal-change-due').textContent = changeDue.toFixed(2);
            } else {
                document.getElementById('modal-change-due').textContent = '0.00';
            }
        });

        // --- 4. NEW: Function to finalize the payment (called by modal buttons) ---
        async function finalizePayment() {
            // Use the stored data
            const { orderId, tableId } = currentModalData;

            try {
                const response = await fetch('api/process_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, table_id: tableId })
                });
                const result = await response.json();

                if (result.success) {
                    // Close modal
                    closePaymentModal();
                    
                    // Remove row from table
                    document.getElementById(`order-row-${orderId}`).remove();
                    
                    // === NEW: Open receipt in a new tab ===
                    window.open(`receipt.php?order_id=${orderId}`, '_blank');

                } else {
                    alert('Error processing payment: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // --- 5. Add listeners for the modal buttons ---
        document.addEventListener('DOMContentLoaded', () => {
            fetchUnpaidOrders(); // Load orders
            setInterval(fetchUnpaidOrders, 15000); // Refresh list

            // Add click listeners to the two payment buttons
            document.getElementById('modal-pay-cash-btn').addEventListener('click', finalizePayment);
            document.getElementById('modal-pay-card-btn').addEventListener('click', finalizePayment);
        });
    </script>
</body>
</html>

