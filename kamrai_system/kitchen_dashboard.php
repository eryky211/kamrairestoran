<?php 
require_once 'auth.php'; 
check_role(['waiter', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard - Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Kitchen Dashboard (KDS)</h1>
        </header>

        <main class="kitchen-layout">
            
            <!-- Column for new 'Pending' orders -->
            <section class="order-column">
                <h2>New Orders (Pending)</h2>
                <div id="pending-orders-list" class="order-list">
                    <!-- Orders load here -->
                </div>
            </section>
            
            <!-- Column for 'Preparing' orders -->
            <section class="order-column">
                <h2>Now Preparing</h2>
                <div id="preparing-orders-list" class="order-list">
                    <!-- Orders load here -->
                </div>
            </section>

        </main>
    </div> <!-- End .main-content -->

    <script>
        // This function builds the HTML for a single order card
        function createOrderCard(order) {
            const card = document.createElement('div');
            card.className = 'order-card';
            card.id = `order-${order.order_id}`;

            let itemsHtml = '<ul class="order-items">';
            order.items.forEach(item => {
                itemsHtml += `<li><strong>${item.quantity}x</strong> ${item.item_name}</li>`;
            });
            itemsHtml += '</ul>';

            let buttonHtml = '';
            if (order.order_status === 'Pending') {
                buttonHtml = `<button class="btn-preparing" onclick="updateStatus(${order.order_id}, 'Preparing')">Start Preparing</button>`;
            } else if (order.order_status === 'Preparing') {
                buttonHtml = `<button class="btn-served" onclick="updateStatus(${order.order_id}, 'Served')">Mark as Served</button>`;
            }

            card.innerHTML = `
                <div class="card-header">
                    <strong>Table: ${order.table_number}</strong>
                    <span>Order #${order.order_id}</span>
                </div>
                ${itemsHtml}
                <div class="card-footer">
                    ${buttonHtml}
                </div>
            `;
            return card;
        }

        // This function updates the order status by calling our new API
        async function updateStatus(orderId, newStatus) {
            try {
                const response = await fetch('api/update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, new_status: newStatus })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const card = document.getElementById(`order-${orderId}`);
                    if (newStatus === 'Preparing') {
                        document.getElementById('preparing-orders-list').appendChild(card);
                        card.querySelector('.card-footer').innerHTML = 
                            `<button class="btn-served" onclick="updateStatus(${orderId}, 'Served')">Mark as Served</button>`;
                    } else if (newStatus === 'Served') {
                        card.remove();
                    }
                } else {
                    alert('Error updating status: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // This function fetches all active kitchen orders from the API
        async function fetchKitchenOrders() {
            try {
                const response = await fetch('api/get_kitchen_orders.php');
                const orders = await response.json();

                const pendingList = document.getElementById('pending-orders-list');
                const preparingList = document.getElementById('preparing-orders-list');

                const existingOrderIds = new Set();
                document.querySelectorAll('.order-card').forEach(card => {
                    existingOrderIds.add(card.id);
                });

                pendingList.innerHTML = ''; 
                preparingList.innerHTML = '';

                orders.forEach(order => {
                    const card = createOrderCard(order);
                    const cardId = `order-${order.order_id}`;
                    
                    if (order.order_status === 'Pending') {
                        pendingList.appendChild(card);
                    } else if (order.order_status === 'Preparing') {
                        preparingList.appendChild(card);
                    }
                    
                    if (existingOrderIds.has(cardId)) {
                        existingOrderIds.delete(cardId);
                    }
                });

                existingOrderIds.forEach(cardId => {
                    const card = document.getElementById(cardId);
                    if (card) card.remove();
                });

            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchKitchenOrders();
            setInterval(fetchKitchenOrders, 10000); 
        });
    </script>
</body>
</html>
