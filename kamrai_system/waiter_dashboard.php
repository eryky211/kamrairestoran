<?php 
require_once 'auth.php'; 
check_role(['waiter', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiter Dashboard - Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css"> 
</head>
<body>

    <?php 
    // This file MUST be in the same folder as waiter_dashboard.php
    require_once 'sidebar.php'; 
    ?>

    <div class="main-content">

        <header>
            <h1>Waiter Dashboard</h1>
        </header>

        <main>
            <h2>Table Status</h2>
            <div id="table-grid-container">
                <p>Loading tables...</p>
            </div>
        </main>

    </div> <script>
        // Function to fetch and display tables
        async function fetchTables() {
            try {
                // Fetch data from our API endpoint
                const response = await fetch('api/get_tables.php');
                const tables = await response.json();

                const grid = document.getElementById('table-grid-container');
                grid.innerHTML = ''; // Clear the 'Loading...' text

                // Loop through each table and create a button
                tables.forEach(table => {
                    const tableButton = document.createElement('a');
                    tableButton.className = 'table-button ' + table.status.toLowerCase();
                    tableButton.href = `create_order.php?table_id=${table.table_id}`;
                    
                    tableButton.innerHTML = `
                        <strong>${table.table_number}</strong>
                        <span>${table.status}</span>
                    `;
                    grid.appendChild(tableButton);
                });

            } catch (error) {
                console.error('Error fetching tables:', error);
                document.getElementById('table-grid-container').innerHTML = '<p>Could not load tables. Please refresh.</p>';
            }
        }

        // Load tables when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            fetchTables();
            
            // Optional: Refresh tables every 10 seconds
            // setInterval(fetchTables, 10000); 
        });
    </script>
</body>
</html>