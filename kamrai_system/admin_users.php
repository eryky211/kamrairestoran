<?php 
require_once 'auth.php'; 
check_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Manage Users</h1>
            <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
        </header>

        <main class="admin-layout">
            <!-- Column 1: Add New User -->
            <section class="admin-form-container">
                <h3>Add New User</h3>
                <form id="add-user-form" class="admin-form">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                    
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">-- Select a Role --</option>
                        <option value="waiter">Waiter</option>
                        <option value="kitchen">Kitchen</option>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
                    
                    <button type="submit">Create User</button>
                    <p id="user-form-message"></p>
                </form>
            </section>

            <!-- Column 2: Existing Users -->
            <section class="admin-list-container">
                <h2>Existing Users</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="user-list-tbody">
                        <!-- Users loaded by JS -->
                    </tbody>
                </table>
            </section>
        </main>
    </div> <!-- End .main-content -->

    <script>
        // Function to load all users
        async function loadUsers() {
            try {
                const response = await fetch('api/get_users.php');
                const users = await response.json();
                const tbody = document.getElementById('user-list-tbody');
                tbody.innerHTML = '';
                
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.full_name}</td>
                        <td>${user.username}</td>
                        <td>${user.role}</td>
                        <td><button class="btn-delete" onclick="deleteUser(${user.user_id})">Delete</button></td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        // Function to handle new user form
        document.getElementById('add-user-form').addEventListener('submit', async (e) => {
            e.preventDefault(); 
            const form = e.target;
            const formData = new FormData(form);
            const msgEl = document.getElementById('user-form-message');
            msgEl.textContent = '';

            try {
                const response = await fetch('api/add_user.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    msgEl.textContent = 'User created successfully!';
                    msgEl.style.color = 'green';
                    form.reset();
                    loadUsers(); 
                } else {
                    msgEl.textContent = 'Error: ' + result.error;
                    msgEl.style.color = 'red';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Function to delete a user
        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user?')) return;
            
            if(userId == <?php echo $_SESSION['user_id']; ?>) { 
                alert("You cannot delete the user you are currently logged in as."); 
                return; 
            }
            
            // Note: We still need to create the delete_user.php API
            alert('Delete functionality to be built. API call for user_id ' + userId);
        }

        // Load users on page start
        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>
</html>
