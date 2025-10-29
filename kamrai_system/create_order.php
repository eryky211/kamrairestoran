<?php 
require_once 'auth.php'; 
// Allow 'waiter' OR 'admin'
check_role(['waiter', 'admin']); 

// Get table ID from the URL, e.g., create_order.php?table_id=5
$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;

if ($table_id === 0) {
    die("Error: No table ID specified.");
}

// === THIS IS THE KEY CHANGE ===
// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id']; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order for Table <?php echo $table_id; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">

        <header>
            <h1>New Order: Table <?php echo $table_id; ?></h1>
            <!-- Back button is in the sidebar -->
        </header>

        <main class="order-layout">
            <!-- Column 1: Menu Items -->
            <section id="menu-container">
                <h2>Menu</h2>
                <div id="menu-items-list">
                    <!-- Menu items will be loaded here by JavaScript -->
                </div>
            </section>

            <!-- Column 2: Current Order (Cart) -->
            <section id="cart-container">
                <h2>Current Order</h2>
                <ul id="cart-items-list">
                    <!-- Cart items will be added here -->
                </ul>
                <div class="cart-summary">
                    <strong>Total: MYR </strong>
                    <strong id="cart-total">0.00</strong>
                </div>
                <button id="submit-order-btn">Submit Order to Kitchen</button>
            </section>
        </main>
    
    </div> <!-- End .main-content -->

    <script>
        // Use a simple 'cart' object to keep track of the order
        // key = item_id, value = { name, price, quantity }
        let cart = {}; 
        const tableId = <?php echo $table_id; ?>;
        // === THIS IS THE KEY CHANGE ===
        // Pass the session user_id to JavaScript
        const userId = <?php echo $user_id; ?>; 

        // 1. Function to add item to cart
        function addToCart(itemId, itemName, itemPrice) {
            if (cart[itemId]) {
                // Item already in cart, increase quantity
                cart[itemId].quantity++;
            } else {
                // New item, add to cart
                cart[itemId] = {
                    name: itemName,
                    price: parseFloat(itemPrice),
                    quantity: 1
                };
            }
            // After adding, redraw the cart display
            renderCart();
        }

        // 2. Function to render the cart on the screen
        function renderCart() {
            const cartList = document.getElementById('cart-items-list');
            const cartTotalEl = document.getElementById('cart-total');
            let total = 0;
            
            cartList.innerHTML = ''; // Clear the cart list

            // Loop through our cart object
            for (const itemId in cart) {
                const item = cart[itemId];
                const li = document.createElement('li');
                li.className = 'cart-item';
                
                li.innerHTML = `
                    <span>(${item.quantity}x) ${item.name}</span>
                    <span>MYR ${(item.price * item.quantity).toFixed(2)}</span>
                `;
                cartList.appendChild(li);
                
                // Add to the grand total
                total += item.price * item.quantity;
            }
            
            cartTotalEl.textContent = total.toFixed(2);
        }

        // 3. Function to load the menu from our API
        async function loadMenu() {
            try {
                const response = await fetch('api/get_menu.php');
                const menu = await response.json();
                const menuList = document.getElementById('menu-items-list');
                
                menuList.innerHTML = ''; // Clear 'loading'
                
                // Loop through each Category
                for (const categoryName in menu) {
                    const category = menu[categoryName];
                    if (category.items.length === 0) continue; // Skip empty categories

                    const categoryHeader = document.createElement('h3');
                    categoryHeader.textContent = categoryName;
                    menuList.appendChild(categoryHeader);
                    
                    // Loop through items in that category
                    category.items.forEach(item => {
                        const itemButton = document.createElement('button');
                        itemButton.className = 'menu-item-btn';
                        itemButton.textContent = `${item.item_name} - MYR ${item.price}`;
                        
                        // Add click event to add item to cart
                        itemButton.onclick = () => {
                            addToCart(item.item_id, item.item_name, item.price);
                        };
                        
                        menuList.appendChild(itemButton);
                    });
                }
            } catch (error) {
                console.error('Error loading menu:', error);
                document.getElementById('menu-items-list').innerHTML = '<p>Error loading menu.</p>';
            }
        }

        // 4. Function to submit the final order
        async function submitOrder() {
            if (Object.keys(cart).length === 0) {
                alert("Cannot submit an empty order.");
                return;
            }

            const orderData = {
                table_id: tableId,
                // === THIS IS THE KEY CHANGE ===
                // Send the logged-in user's ID
                user_id: userId, 
                items: cart 
            };
            
            try {
                const response = await fetch('api/submit_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Order Submitted Successfully!');
                    // Redirect back to the dashboard
                    window.location.href = 'waiter_dashboard.php';
                } else {
                    alert('Error submitting order: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('A network error occurred. Please try again.');
            }
        }

        // Load the menu when the page is ready
        document.addEventListener('DOMContentLoaded', loadMenu);
        // Add click listener for the submit button
        document.getElementById('submit-order-btn').addEventListener('click', submitOrder);
    </script>
</body>
</html>

