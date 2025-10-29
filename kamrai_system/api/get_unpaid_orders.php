<?php
require_once '../db.php';

try {
    $sql = "
        SELECT 
            o.order_id, 
            o.table_id, 
            o.order_time, 
            o.total_price, 
            o.order_status,
            t.table_number
        FROM Orders o
        JOIN RestaurantTables t ON o.table_id = t.table_id
        WHERE o.payment_status = 'Unpaid'
        ORDER BY o.order_time ASC
    ";
    
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($orders);

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>