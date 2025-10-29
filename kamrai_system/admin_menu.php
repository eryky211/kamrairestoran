<?php 
require_once 'auth.php'; 
check_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Menu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Manage Menu</h1>
        </header>

        <main class="admin-layout">

            <!-- Column 1: Add New Items/Categories -->
            <section class="admin-form-container">
                <!-- Add Category Form -->
                <h3>Add New Category</h3>
                <form id="add-category-form" class="admin-form">
                    <label for="category-name">Category Name:</label>
                    <input type="text" id="category-name" name="category_name" required>
                    <button type="submit">Add Category</button>
                </form>

                <hr class="form-divider">

                <!-- Add Menu Item Form -->
                <h3>Add New Menu Item</h3>
                <form id="add-item-form" class="admin-form">
                    <label for="item-name">Item Name:</label>
                    <input type="text" id="item-name" name="item_name" required>
                    
                    <label for="item-price">Price (MYR):</label>
                    <input type="number" id="item-price" name="price" step="0.01" min="0" required>
                    
                    <label for="item-category">Category:</label>
                    <select id="item-category" name="category_id" required>
                        <!-- Categories will be loaded here -->
                        <option value="">Loading categories...</option>
                    </select>
                    
                    <label for="item-desc">Description (Optional):</label>
                    <textarea id="item-desc" name="description" rows="3"></textarea>
                    
                    <button type="submit">Add Item</button>
                </form>
            </section>

            <!-- Column 2: Existing Menu -->
            <section class="admin-list-container">
                <h2>Existing Menu</h2>
                <div id="existing-menu-list">
                    <p>Loading menu...</p>
                </div>
            </section>

        </main>
    </div> <!-- End .main-content -->

    <!-- Edit Item Modal -->
    <div id="edit-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Menu Item</h2>
            <form id="edit-item-form" class="admin-form">
                <input type="hidden" id="edit-item-id" name="item_id">
                
                <label for="edit-item-name">Item Name:</label>
                <input type="text" id="edit-item-name" name="item_name" required>
                
                <label for="edit-item-price">Price (MYR):</label>
                <input type="number" id="edit-item-price" name="price" step="0.01" min="0" required>
                
                <label for="edit-item-category">Category:</label>
                <select id="edit-item-category" name="category_id" required>
                    <!-- Categories will be loaded here -->
                </select>
                
                <label for="edit-item-desc">Description (Optional):</label>
                <textarea id="edit-item-desc" name="description" rows="3"></textarea>
                
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Global variable to store categories
        let categories = [];

        // --- NEW: Load all menu and category data ---
        async function loadAdminMenu() {
            try {
                const response = await fetch('api/get_full_menu.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const menuData = await response.json();
                
                const menuList = document.getElementById('existing-menu-list');
                const categorySelect = document.getElementById('item-category');
                const editCategorySelect = document.getElementById('edit-item-category');
                
                menuList.innerHTML = ''; // Clear "Loading..."
                categorySelect.innerHTML = '<option value="">-- Select a Category --</option>';
                editCategorySelect.innerHTML = '';
                
                categories = menuData.categories; // Store categories from new API response

                // Populate the <select> dropdowns
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.category_id;
                    option.textContent = cat.category_name;
                    categorySelect.appendChild(option);
                    editCategorySelect.appendChild(option.cloneNode(true));
                });

                // Populate the "Existing Menu" list
                if (menuData.menu.length === 0) {
                     menuList.innerHTML = '<p>No menu items found. Start by adding categories and items.</p>';
                     return;
                }
                
                menuData.menu.forEach(cat => {
                    if (cat.items.length === 0) return; // Skip empty
                    
                    const catDiv = document.createElement('div');
                    catDiv.className = 'menu-category-admin';
                    catDiv.innerHTML = `<h3>${cat.category_name}</h3>`;
                    
                    const table = document.createElement('table');
                    table.className = 'admin-item-table';
                    table.innerHTML = `
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>
                    `;
                    
                    // Use createElement for robust event listeners
                    cat.items.forEach(item => {
                        const row = table.insertRow();
                        row.innerHTML = `
                            <td>${item.item_name}</td>
                            <td>MYR ${parseFloat(item.price).toFixed(2)}</td>
                        `;
                        
                        // Availability Checkbox
                        const cellAvailable = row.insertCell();
                        const checkAvailable = document.createElement('input');
                        checkAvailable.type = 'checkbox';
                        checkAvailable.checked = item.is_available;
                        checkAvailable.onchange = () => {
                            toggleAvailability(item.item_id, checkAvailable.checked);
                        };
                        cellAvailable.appendChild(checkAvailable);

                        // Actions Cell (Edit/Delete)
                        const cellActions = row.insertCell();
                        cellActions.className = 'item-actions';
                        
                        const btnEdit = document.createElement('button');
                        btnEdit.className = 'btn-edit';
                        btnEdit.textContent = 'Edit';
                        btnEdit.onclick = () => {
                            // Pass the full 'item' object
                            openEditModal(item);
                        };
                        cellActions.appendChild(btnEdit);
                        
                        const btnDelete = document.createElement('button');
                        btnDelete.className = 'btn-delete';
                        btnDelete.textContent = 'Delete';
                        btnDelete.onclick = () => {
                            deleteMenuItem(item.item_id, item.item_name);
                        };
                        cellActions.appendChild(btnDelete);
                    });
                    
                    catDiv.appendChild(table);
                    menuList.appendChild(catDiv);
                });

            } catch (error) {
                console.error('Error loading menu:', error);
                document.getElementById('existing-menu-list').innerHTML = '<p>Error loading menu. Please try refreshing.</p>';
            }
        }

        // Function to handle adding a new category
        document.getElementById('add-category-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('api/add_category.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Category added!');
                    form.reset();
                    loadAdminMenu(); // Refresh the menu
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Function to handle adding a new menu item
        document.getElementById('add-item-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('api/add_menu_item.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Menu item added!');
                    form.reset();
                    loadAdminMenu(); // Refresh the menu
                } else {
                    // This typo was fixed
                    alert('Error: ' + result.error); 
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: Something went wrong. ' + error.message);
            }
        });

        // Function to toggle item availability
        async function toggleAvailability(itemId, isAvailable) {
            try {
                const response = await fetch('api/update_item_availability.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: itemId, is_available: isAvailable })
                });
                const result = await response.json();
                if (!result.success) {
                    alert('Error updating item: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // --- Edit Modal Functions ---
        function openEditModal(item) {
            document.getElementById('edit-item-id').value = item.item_id;
            document.getElementById('edit-item-name').value = item.item_name;
            document.getElementById('edit-item-price').value = item.price;
            document.getElementById('edit-item-category').value = item.category_id;
            document.getElementById('edit-item-desc').value = item.description;
            document.getElementById('edit-modal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('edit-modal').style.display = 'none';
        }

        document.getElementById('edit-item-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('api/update_menu_item.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Item updated successfully!');
                    closeEditModal();
                    loadAdminMenu(); // Refresh the menu
                } else {
                    alert('Error updating item: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // --- Delete Item Function ---
        async function deleteMenuItem(itemId, itemName) {
            if (!confirm(`Are you sure you want to delete "${itemName}"?\nThis action cannot be undone.`)) {
                return;
            }
            
            try {
                const response = await fetch('api/delete_menu_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: itemId })
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Item deleted successfully.');
                    loadAdminMenu(); // Refresh the menu
                } else {
                    alert('Error deleting item: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Load the menu when the page is ready
        document.addEventListener('DOMContentLoaded', loadAdminMenu);
    </script>
</body>
</html>

