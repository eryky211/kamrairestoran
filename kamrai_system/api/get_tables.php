<?php
// Include the database connection
require_once '../db.php';

try {
    // Prepare and execute the query
    $stmt = $pdo->query("SELECT table_id, table_number, status FROM RestaurantTables ORDER BY table_number");
    $tables = $stmt->fetchAll();
    
    // Set header to return JSON
    header('Content-Type: application/json');
    
    // Output the data as JSON
    echo json_encode($tables);
    
} catch(PDOException $e) {
    // Handle error
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}
?>