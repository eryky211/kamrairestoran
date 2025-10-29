<?php
require_once '../auth.php';
check_role('admin');
require_once '../db.php';
header('Content-Type: application/json');

// We use $_POST because we are sending FormData from the edit form
$item_id = $_POST['edit_item_id'] ?? 0;
$item_name = $_POST['edit_item_name'] ?? '';
$price = $_POST['edit_item_price'] ?? 0;
$category_id = $_POST['edit_item_category'] ?? 0;
$description = $_POST['edit_item_desc'] ?? '';

if (empty($item_id) || empty($item_name) || empty($price) || empty($category_id)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

try {
    $sql = "UPDATE MenuItems 
            SET item_name = ?, 
                price = ?, 
                category_id = ?, 
                description = ?
            WHERE item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_name, $price, $category_id, $description, $item_id]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
