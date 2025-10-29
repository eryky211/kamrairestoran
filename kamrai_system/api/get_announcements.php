<?php
// This API is now public, so it does NOT need auth.php.
// It just needs to connect to the database.
require_once '../db.php';

// Set header to JSON
header('Content-Type: application/json');

$response_data = [
    'box1' => '',
    'box2' => ''
];

try {
    // A more efficient way to get both announcements
    $sql = "SELECT announcement_key, announcement_text FROM Announcements WHERE announcement_key IN ('box1', 'box2')";
    $stmt = $pdo->query($sql);
    
    // Fetches as ['box1' => 'text1', 'box2' => 'text2']
    $announcements = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 

    if (!empty($announcements)) {
        $response_data['box1'] = $announcements['box1'] ?? '';
        $response_data['box2'] = $announcements['box2'] ?? '';
    }

    echo json_encode($response_data);

} catch (Exception $e) {
    // If something goes wrong, send a *valid* JSON error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>