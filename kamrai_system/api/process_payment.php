<?php
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$order_id = $data['order_id'] ?? 0;
$table_id = $data['table_id'] ?? 0;

if (empty($order_id) || empty($table_id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid Order ID or Table ID.']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Step 1: Update the Order status to 'Paid'
    $stmt_order = $pdo->prepare("UPDATE Orders SET payment_status = 'Paid' WHERE order_id = ?");
    $stmt_order->execute([$order_id]);

    // Step 2: Update the Table status to 'Available'
    $stmt_table = $pdo->prepare("UPDATE RestaurantTables SET status = 'Available' WHERE table_id = ?");
    $stmt_table->execute([$table_id]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Something went wrong, roll back
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>