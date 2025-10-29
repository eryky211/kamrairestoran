<?php
require_once '../db.php';
require_once '../auth.php'; 
check_role('admin');
header('Content-Type: application/json');

$table_number = $_POST['table_number'] ?? '';

if (empty($table_number)) {
    echo json_encode(['success' => false, 'error' => 'Table name/number is required.']);
    exit;
}

try {
    // Default status is 'Available'
    $sql = "INSERT INTO RestaurantTables (table_number, status) VALUES (?, 'Available')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$table_number]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    // Check for duplicate
    if ($e->getCode() == 23000) { 
        echo json_encode(['success' => false, 'error' => 'That table name/number already exists.']);
    } else {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>