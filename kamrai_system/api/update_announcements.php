<?php
// This is a secure, admin-only API
require_once '../auth.php';
check_role('admin');

require_once '../db.php';
header('Content-Type: application/json');

// Get the text from the form
$box1_text = $_POST['box1'] ?? null;
$box2_text = $_POST['box2'] ?? null;

if ($box1_text === null || $box2_text === null) {
    echo json_encode(['success' => false, 'error' => 'Missing data.']);
    exit;
}

try {
    // We use a transaction to make sure both updates succeed or fail together
    $pdo->beginTransaction();

    // Update Box 1
    $stmt1 = $pdo->prepare("UPDATE Announcements SET announcement_text = ? WHERE announcement_key = 'box1'");
    $stmt1->execute([$box1_text]);

    // Update Box 2
    $stmt2 = $pdo->prepare("UPDATE Announcements SET announcement_text = ? WHERE announcement_key = 'box2'");
    $stmt2->execute([$box2_text]);

    // If both are successful, commit the changes
    $pdo->commit();
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // If anything went wrong, roll back
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
