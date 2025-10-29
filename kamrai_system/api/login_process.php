<?php
// We must start the session on every page that uses it
session_start();
require_once '../db.php';
header('Content-Type: application/json');

// Check if user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    echo json_encode(['success' => false, 'error' => 'Already logged in.']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Username and password are required.']);
    exit;
}

try {
    // Find the user by username
    $sql = "SELECT user_id, username, password_hash, role FROM Users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify user exists and password is correct
    // password_verify() securely checks the hash
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Password is correct! Start a new session.
        session_regenerate_id(); // Prevents session fixation
        
        // Store data in session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["user_id"] = $user['user_id'];
        $_SESSION["username"] = $user['username'];
        $_SESSION["role"] = $user['role'];
        
        // Determine redirect location based on role
        $redirect_url = 'index.php'; // Default
        switch ($user['role']) {
            case 'waiter':
                $redirect_url = 'waiter_dashboard.php';
                break;
            case 'kitchen':
                $redirect_url = 'kitchen_dashboard.php';
                break;
            case 'cashier':
                $redirect_url = 'cashier_dashboard.php';
                break;
            case 'admin':
                $redirect_url = 'admin_dashboard.php';
                break;
        }

        echo json_encode(['success' => true, 'redirect' => $redirect_url]);

    } else {
        // Invalid username or password
        echo json_encode(['success' => false, 'error' => 'Invalid username or password.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>