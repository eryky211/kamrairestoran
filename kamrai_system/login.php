<?php
// Start the session
session_start();

// If user is already logged in, redirect them to their dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    switch ($_SESSION["role"]) {
        case 'waiter':
            header("location: waiter_dashboard.php");
            break;
        case 'kitchen':
            header("location: kitchen_dashboard.php");
            break;
        case 'cashier':
            header("location: cashier_dashboard.php");
            break;
        case 'admin':
            header("location: admin_dashboard.php");
            break;
        default:
            header("location: index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Kamrai Restaurant System</h2>
        <p>Please log in to continue</p>
        
        <form id="login-form" class="admin-form">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
            <p id="login-error" class="error-message"></p>
        </form>
        <a href="index.php">Back to Homepage</a>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const errorEl = document.getElementById('login-error');
            errorEl.textContent = ''; // Clear previous errors

            try {
                const response = await fetch('api/login_process.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Redirect based on role
                    window.location.href = result.redirect;
                } else {
                    errorEl.textContent = result.error;
                }
            } catch (error) {
                console.error('Error:', error);
                errorEl.textContent = 'An unexpected error occurred.';
            }
        });
    </script>
</body>
</html>