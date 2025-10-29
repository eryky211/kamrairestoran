<?php
require_once '../auth.php';
check_role('admin');
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$item_id = $data['item_id'] ?? 0;

if (empty($item_id)) {
    echo json_encode(['success' => false, 'error' => 'Invalid Item ID.']);
    exit;
}

try {
    // IMPORTANT: We must first delete any records from 'OrderItems'
    // that reference this menu item. If we don't, the database
    // foreign key constraint will block the deletion.
    // A better long-term solution might be to "deactivate" items,
    // but for a simple delete, this is required.
    $stmt_orderitems = $pdo->prepare("DELETE FROM OrderItems WHERE item_id = ?");
    $stmt_orderitems->execute([$item_id]);

    // Now we can safely delete the item from the main MenuItems table
    $stmt_menuitem = $pdo->prepare("DELETE FROM MenuItems WHERE item_id = ?");
    $stmt_menuitem->execute([$item_id]);
    
    // Check if any row was actually deleted
    if ($stmt_menuitem->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Item not found.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
