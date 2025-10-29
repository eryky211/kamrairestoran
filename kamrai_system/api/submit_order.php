<?php
// Auth check: Must be logged in
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in.']);
    exit;
}

require_once '../db.php';
header('Content-Type: application/json');

// Get the raw POST data (which is JSON)
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true); // true for associative array

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data.']);
    exit;
}

// === THIS IS THE KEY CHANGE ===
// Get the user_id from the JSON (instead of hardcoding it)
$table_id = $data['table_id'] ?? 0;
$user_id = $data['user_id'] ?? 0; // Use the ID from the JSON
$items = $data['items'] ?? [];

// Use 'user_id' in the validation
if (empty($table_id) || empty($user_id) || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Missing data (table, user, or items).']);
    exit;
}

try {
    // === Start Database Transaction ===
    $pdo->beginTransaction();

    // Step 1: Update the table status to 'Occupied'
    $stmt_table = $pdo->prepare("UPDATE RestaurantTables SET status = 'Occupied' WHERE table_id = ?");
    $stmt_table->execute([$table_id]);

    // Step 2: Create the main order in the 'Orders' table
    // === THIS IS THE KEY CHANGE ===
    // Save the correct 'user_id' in the 'waiter_id' column
    $stmt_order = $pdo->prepare(
        "INSERT INTO Orders (table_id, waiter_id, order_status, payment_status) 
         VALUES (?, ?, 'Pending', 'Unpaid')"
    );
    $stmt_order->execute([$table_id, $user_id]); // Use $user_id here

    // Get the ID of the order we just created
    $order_id = $pdo->lastInsertId();

    // Step 3: Loop through cart items and add them to 'OrderItems'
    $total_price = 0;
    
    $stmt_items = $pdo->prepare(
        "INSERT INTO OrderItems (order_id, item_id, quantity, item_price_at_order) 
         VALUES (?, ?, ?, ?)"
    );

    foreach ($items as $item_id => $item) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        
        $stmt_items->execute([$order_id, $item_id, $quantity, $price]);
        
        $total_price += ($price * $quantity);
    }

    // Step 4: Update the 'Orders' table with the final calculated price
    $stmt_update_total = $pdo->prepare("UPDATE Orders SET total_price = ? WHERE order_id = ?");
    $stmt_update_total->execute([$total_price, $order_id]);

    // === Commit Transaction ===
    $pdo->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    // === Rollback Transaction ===
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

