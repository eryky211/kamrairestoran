<?php
// We don't need a login check for this, as the order ID is unique
// and this only shows paid orders.
// However, for extra security, you could add:
// require_once '../auth.php'; 
// check_role(['cashier', 'admin']);

require_once '../db.php';
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id === 0) {
    echo json_encode(['error' => 'No Order ID provided.']);
    exit;
}

try {
    // --- 1. Get the main order details ---
    // We join with Users and Tables to get their names
    $sql_order = "
        SELECT 
            o.order_id, 
            o.order_time, 
            o.total_price,
            u.full_name,
            t.table_number
        FROM Orders o
        JOIN Users u ON o.waiter_id = u.user_id
        JOIN RestaurantTables t ON o.table_id = t.table_id
        WHERE o.order_id = ? 
          AND o.payment_status = 'Paid'
    ";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$order_id]);
    $order = $stmt_order->fetch();

    if (!$order) {
        throw new Exception('Order not found or not paid.');
    }

    // --- 2. Get all items for this order ---
    $sql_items = "
        SELECT 
            oi.quantity, 
            oi.item_price_at_order,
            m.item_name
        FROM OrderItems oi
        JOIN MenuItems m ON oi.item_id = m.item_id
        WHERE oi.order_id = ?
    ";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([$order_id]);
    $items = $stmt_items->fetchAll();

    // --- 3. Send the combined data ---
    $response = [
        'order' => $order,
        'items' => $items
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
