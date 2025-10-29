<?php
require_once '../auth.php';
check_role('admin'); // Only admins can access this API

require_once '../db.php';
header('Content-Type: application/json');

try {
    // --- 1. Get All Paid Orders (for the table) ---
    // We join with Users and Tables to get the names
    $sql_orders = "
        SELECT 
            o.order_id, 
            o.order_time, 
            o.total_price,
            u.full_name,
            t.table_number
        FROM Orders o
        JOIN Users u ON o.waiter_id = u.user_id
        JOIN RestaurantTables t ON o.table_id = t.table_id
        WHERE o.payment_status = 'Paid'
        ORDER BY o.order_time DESC
    ";
    $stmt_orders = $pdo->query($sql_orders);
    $orders = $stmt_orders->fetchAll();

    // --- 2. Get Total Sales for TODAY ---
    // We use DATE(order_time) = DATE(NOW()) to get today's sales
    $sql_total = "
        SELECT SUM(total_price) AS total_sales_today
        FROM Orders
        WHERE payment_status = 'Paid'
          AND DATE(order_time) = DATE(NOW())
    ";
    $stmt_total = $pdo->query($sql_total);
    $total_sales = $stmt_total->fetch();

    // Prepare the final response
    $response = [
        'orders' => $orders,
        'total_sales_today' => $total_sales['total_sales_today'] ?? 0.00
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

