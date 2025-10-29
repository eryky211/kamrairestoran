<?php 
require_once 'auth.php'; 
check_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Tables</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Manage Tables</h1>
            <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
        </header>

        <main class="admin-layout">
            <!-- Column 1: Add New Table -->
            <section class="admin-form-container">
                <h3>Add New Table</h3>
                <form id="add-table-form" class="admin-form">
                    <label for="table_number">Table Name/Number:</label>
                    <input type="text" id="table_number" name="table_number" placeholder="e.g., 'Table 1' or 'Patio 2'" required>
                    <button type="submit">Add Table</button>
                    <p id="table-form-message"></p>
                </form>
            </section>

            <!-- Column 2: Existing Tables -->
            <section class="admin-list-container">
                <h2>Existing Tables</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Table Name/Number</th>
                            <th>Current Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-list-tbody">
                        <!-- Tables loaded by JS -->
                    </tbody>
                </table>
            </section>
        </main>
    </div> <!-- End .main-content -->

    <script>
        async function loadTables() {
            try {
                const response = await fetch('api/get_tables.php');
                const tables = await response.json();
                const tbody = document.getElementById('table-list-tbody');
                tbody.innerHTML = '';
                
                tables.forEach(table => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${table.table_number}</td>
                        <td><span class="status-badge ${table.status.toLowerCase()}">${table.status}</span></td>
                        <td><button class="btn-delete" onclick="alert('Delete functionality to be built.')">Delete</button></td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Error loading tables:', error);
            }
        }

        document.getElementById('add-table-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const msgEl = document.getElementById('table-form-message');
            msgEl.textContent = '';

            try {
                const response = await fetch('api/add_table.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    msgEl.textContent = 'Table added successfully!';
                    msgEl.style.color = 'green';
                    form.reset();
                    loadTables(); 
                } else {
                    msgEl.textContent = 'Error: ' + result.error;
                    msgEl.style.color = 'red';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        document.addEventListener('DOMContentLoaded', loadTables);
    </script>
</body>
</html>
