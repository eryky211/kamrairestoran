<?php
require_once '../db.php';
require_once '../auth.php'; 
check_role('admin');
header('Content-Type: application/json');

$full_name = $_POST['full_name'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (empty($full_name) || empty($username) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

// Hash the password for storage
// This is the most important security step!
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO Users (full_name, username, password_hash, role) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$full_name, $username, $password_hash, $role]);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    // Check for duplicate username
    if ($e->getCode() == 23000) { 
        echo json_encode(['success' => false, 'error' => 'Username already exists.']);
    } else {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>