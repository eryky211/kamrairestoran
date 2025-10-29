<?php
require_once '../auth.php';
check_role('admin'); // Only admin can see stats
require_once '../db.php';

try {
    // 1. Occupied Tables
    $stmt_tables = $pdo->query("SELECT COUNT(*) as count FROM RestaurantTables WHERE status = 'Occupied'");
    $occupied_tables = $stmt_tables->fetch()['count'];

    // 2. Pending Orders
    $stmt_pending = $pdo->query("SELECT COUNT(*) as count FROM Orders WHERE order_status = 'Pending'");
    $pending_orders = $stmt_pending->fetch()['count'];

    // 3. Today's Sales (using CURDATE() for MySQL)
    $stmt_sales = $pdo->query("SELECT SUM(total_price) as total FROM Orders WHERE payment_status = 'Paid' AND DATE(order_time) = CURDATE()");
    $sales_today = $stmt_sales->fetch()['total'] ?? 0; // Use ?? 0 in case it's null

    // 4. Today's Orders
    $stmt_orders_today = $pdo->query("SELECT COUNT(*) as count FROM Orders WHERE payment_status = 'Paid' AND DATE(order_time) = CURDATE()");
    $orders_today = $stmt_orders_today->fetch()['count'];

    // 5. Recent Paid Orders
    $stmt_recent = $pdo->query("
        SELECT o.order_id, t.table_number, o.total_price 
        FROM Orders o 
        JOIN RestaurantTables t ON o.table_id = t.table_id 
        WHERE o.payment_status = 'Paid' 
        ORDER BY o.order_time DESC 
        LIMIT 5
    ");
    $recent_orders = $stmt_recent->fetchAll();

    // Package it all up
    $stats = [
        'occupied_tables' => $occupied_tables,
        'pending_orders' => $pending_orders,
        'sales_today' => (float)$sales_today, // Ensure it's a number
        'orders_today' => $orders_today,
        'recent_orders' => $recent_orders
    ];

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'stats' => $stats]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
