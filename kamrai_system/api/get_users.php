<?php
require_once '../db.php';
require_once '../auth.php'; 
check_role('admin');

try {
    $stmt = $pdo->query("SELECT user_id, full_name, username, role FROM Users");
    $users = $stmt->fetchAll();
    
    header('Content-Type: application/json');
    echo json_encode($users);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>