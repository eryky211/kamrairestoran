<?php
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$order_id = $data['order_id'] ?? 0;
$new_status = $data['new_status'] ?? '';

// Basic validation
if (empty($order_id) || empty($new_status)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}

// Security: Only allow specific status changes
$allowed_statuses = ['Preparing', 'Served'];
if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status.']);
    exit;
}

try {
    $sql = "UPDATE Orders SET order_status = ? WHERE order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_status, $order_id]);
    
    echo json_encode(['success' => true, 'order_id' => $order_id, 'new_status' => $new_status]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>