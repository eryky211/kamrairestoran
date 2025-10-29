<?php
// Go UP ONE LEVEL (..) to find auth.php
require_once '../auth.php';
// Check that the user is an 'admin'
check_role('admin');

// Go UP ONE LEVEL (..) to find db.php
require_once '../db.php';

header('Content-Type: application/json');

try {
    // 1. Get all categories
    $cat_stmt = $pdo->query("SELECT * FROM Categories ORDER BY category_name");
    $categories = $cat_stmt->fetchAll();

    // 2. Get all items (not just available ones)
    $item_stmt = $pdo->query("SELECT * FROM MenuItems ORDER BY item_name");
    $items = $item_stmt->fetchAll();

    // 3. Build the menu structure
    $menu = [];
    foreach ($categories as $category) {
        $cat_data = [
            'category_id' => $category['category_id'],
            'category_name' => $category['category_name'],
            'items' => []
        ];

        foreach ($items as $item) {
            if ($item['category_id'] == $category['category_id']) {
                // Cast is_available to a boolean for proper JSON
                $item['is_available'] = (bool)$item['is_available'];
                $cat_data['items'][] = $item;
            }
        }
        $menu[] = $cat_data;
    }

    // 4. Send back both categories and the structured menu
    // The admin_menu.php JavaScript needs this exact structure
    $response = [
        'categories' => $categories,
        'menu' => $menu
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

