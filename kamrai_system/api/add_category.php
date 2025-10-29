<?php
require_once '../db.php';
header('Content-Type: application/json');

// We use $_POST here because we are sending FormData
$category_name = $_POST['category_name'] ?? '';

if (empty($category_name)) {
    echo json_encode(['success' => false, 'error' => 'Category name is required.']);
    exit;
}

try {
    $sql = "INSERT INTO Categories (category_name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_name]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    // Check for duplicate entry
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'error' => 'Category name already exists.']);
    } else {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>