<?php
// Start the session
session_start();

// --- Configuration ---
// Define the base path of your application
// If your app is at http://localhost/kamrai_system/, your base path is /kamrai_system/
// If it's at http://localhost/, your base path is just /
$base_path = '/kamrai_system/'; 
// ---------------------

// --- Paths ---
$login_page = $base_path . 'login.php';
$index_page = $base_path . 'index.php';

// Check if the user is logged in.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // We must send a full URL for redirects to work from any folder
    $host = $_SERVER['HTTP_HOST'];
    $url = "http://" . $host . $login_page;
    
    // If this is an API call (AJAX), don't redirect, just send an error
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated.']);
    } else {
        // Otherwise, redirect the browser
        header("Location: $url");
    }
    exit;
}

// === UPDATED FUNCTION ===
// Function to check if user has one of the allowed roles
function check_role($allowed_roles = []) {
    // Force $allowed_roles to be an array, even if a single string is passed
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    // Check if the user's role is in the allowed list
    if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], $allowed_roles)) {
        // If not, redirect
        $host = $_SERVER['HTTP_HOST'];
        global $index_page; // Get path from config above
        $url = "http://" . $host . $index_page;
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'You do not have permission.']);
        } else {
            header("Location: $url");
        }
        exit;
    }
}
?>
