<?php
require_once '../db.php';

try {
    // 1. Get all 'Pending' and 'Preparing' orders
    $order_sql = "
        SELECT 
            o.order_id, o.order_status, t.table_number
        FROM Orders o
        JOIN RestaurantTables t ON o.table_id = t.table_id
        WHERE o.order_status = 'Pending' OR o.order_status = 'Preparing'
        ORDER BY o.order_time ASC
    ";
    $stmt_orders = $pdo->query($order_sql);
    $orders = $stmt_orders->fetchAll();

    if (empty($orders)) {
        // Send back an empty array if no active orders
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    // 2. Get all order IDs into a simple array
    $order_ids = [];
    foreach ($orders as $order) {
        $order_ids[] = $order['order_id'];
    }
    
    // Create placeholders for the IN() clause
    $placeholders = rtrim(str_repeat('?,', count($order_ids)), ',');

    // 3. Get all items for ALL those orders in one query
    $item_sql = "
        SELECT 
            oi.order_id, oi.quantity, m.item_name
        FROM OrderItems oi
        JOIN MenuItems m ON oi.item_id = m.item_id
        WHERE oi.order_id IN ($placeholders)
    ";
    $stmt_items = $pdo->prepare($item_sql);
    $stmt_items->execute($order_ids);
    $items = $stmt_items->fetchAll();

    // 4. Combine the orders and items into a structured array
    $orders_with_items = [];
    foreach ($orders as $order) {
        $order['items'] = []; // Add an 'items' array to each order
        
        foreach ($items as $item) {
            if ($item['order_id'] == $order['order_id']) {
                $order['items'][] = $item;
            }
        }
        $orders_with_items[] = $order;
    }

    // 5. Send the final JSON
    header('Content-Type: application/json');
    echo json_encode($orders_with_items);

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>