<?php
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$item_id = $data['item_id'] ?? 0;
// We receive true/false from JSON, convert it to 1/0 for SQL
$is_available = $data['is_available'] ? 1 : 0; 

if (empty($item_id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid Item ID.']);
    exit;
}

try {
    $sql = "UPDATE MenuItems SET is_available = ? WHERE item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$is_available, $item_id]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>