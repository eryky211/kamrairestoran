<?php
require_once '../db.php';

try {
    // We fetch categories first
    $cat_stmt = $pdo->query("SELECT * FROM Categories ORDER BY category_name");
    $categories = $cat_stmt->fetchAll();

    // Then we fetch all available menu items
    $item_stmt = $pdo->query("SELECT * FROM MenuItems WHERE is_available = TRUE ORDER BY item_name");
    $items = $item_stmt->fetchAll();

    // We will structure the final JSON by category
    $menu = [];
    foreach ($categories as $category) {
        $menu[$category['category_name']] = [
            'category_id' => $category['category_id'],
            'items' => []
        ];
    }

    // Add items into their respective categories
    foreach ($items as $item) {
        // Find the category name for this item
        foreach ($categories as $category) {
            if ($category['category_id'] == $item['category_id']) {
                $category_name = $category['category_name'];
                // Add the item to the correct category in our $menu array
                $menu[$category_name]['items'][] = $item;
                break;
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($menu);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>