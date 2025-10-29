<?php 
require_once 'auth.php'; 
check_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>

        <main>
            <!-- 1. "At a Glance" Stat Cards -->
            <h2>At a Glance</h2>
            <div class="stat-grid">
                <div class="stat-card">
                    <h3>Occupied Tables</h3>
                    <p id="stat-occupied-tables">...</p>
                </div>
                <div class="stat-card">
                    <h3>Pending in Kitchen</h3>
                    <p id="stat-pending-orders">...</p>
                </div>
                <div class="stat-card">
                    <h3>Today's Orders</h3>
                    <p id="stat-orders-today">...</p>
                </div>
                <div class="stat-card sales">
                    <h3>Today's Sales</h3>
                    <p id="stat-sales-today">...</p>
                </div>
            </div>

            <div class="admin-layout">
                <!-- 2. Recent Activity -->
                <section class="admin-list-container" style="flex: 1.5;">
                    <h2>Recent Activity (Last 5 Paid Orders)</h2>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Table</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="recent-orders-list">
                            <tr><td colspan="3">Loading...</td></tr>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </div> <!-- End .main-content -->

    <script>
    async function loadAdminStats() {
        try {
            const response = await fetch('api/get_admin_stats.php');
            const result = await response.json();

            if (result.success) {
                const stats = result.stats;

                // 1. Populate Stat Cards
                document.getElementById('stat-occupied-tables').textContent = stats.occupied_tables;
                document.getElementById('stat-pending-orders').textContent = stats.pending_orders;
                document.getElementById('stat-orders-today').textContent = stats.orders_today;
                document.getElementById('stat-sales-today').textContent = `MYR ${stats.sales_today.toFixed(2)}`;

                // 2. Populate Recent Orders
                const recentList = document.getElementById('recent-orders-list');
                recentList.innerHTML = '';
                if (stats.recent_orders.length > 0) {
                    stats.recent_orders.forEach(order => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${order.order_id}</td>
                            <td>${order.table_number}</td>
                            <td>MYR ${parseFloat(order.total_price).toFixed(2)}</td>
                        `;
                        recentList.appendChild(row);
                    });
                } else {
                    recentList.innerHTML = '<tr><td colspan="3">No paid orders found today.</td></tr>';
                }
            } else {
                console.error('Error loading stats:', result.error);
            }
        } catch (error) {
            console.error('Network error:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAdminStats();
        // Refresh stats every 30 seconds
        setInterval(loadAdminStats, 30000);
    });
    </script>
</body>
</html>

