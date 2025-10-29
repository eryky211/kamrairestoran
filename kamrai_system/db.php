<?php
// Database connection settings
$servername = "localhost"; // Or your server address (e.g., 127.0.0.1)
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "kamrairestaurant";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Stop script and display error
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>