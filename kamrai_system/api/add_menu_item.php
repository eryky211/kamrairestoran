<?php
require_once '../db.php';
header('Content-Type: application/json');

// Use $_POST for FormData
$item_name = $_POST['item_name'] ?? '';
$price = $_POST['price'] ?? 0;
$category_id = $_POST['category_id'] ?? 0;
$description = $_POST['description'] ?? '';

if (empty($item_name) || empty($price) || empty($category_id)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

try {
    $sql = "INSERT INTO MenuItems (item_name, price, category_id, description) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_name, $price, $category_id, $description]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>