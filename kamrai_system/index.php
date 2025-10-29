<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Kamrai Restaurant</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="homepage-body">

    <!-- === NEW: Announcement Box 1 (Top Left) === -->
    <div class="announcement-box box-top-left">
        <h4 class="announcement-title">Announcement</h4>
        <p id="announcement-box1-text">Loading...</p>
    </div>

    <!-- === NEW: Announcement Box 2 (Top Right) === -->
    <div class="announcement-box box-top-right">
        <h4 class="announcement-title">Daily Special</h4>
        <p id="announcement-box2-text">Loading...</p>
    </div>


    <div class="homepage-container">
        <!-- This is the logo you added -->
        <div class="logo-container">
            <img src="assets/logo.png" alt="Kamrai System Logo" class="homepage-logo" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <!-- Fallback text if logo fails to load -->
            <h2 class="logo-fallback" style="display:none;">Kamrai Restaurant</h2>
        </div>

        <h1>Welcome to Kamrai Restaurant</h1>
        <p>Your new digital ordering system.</p>
        
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            
            <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?>!</strong></p>
            
            <?php // Provide correct link based on role
                $dashboard_link = 'index.php';
                switch ($_SESSION["role"]) {
                    case 'waiter': $dashboard_link = 'waiter_dashboard.php'; break;
                    case 'kitchen': $dashboard_link = 'kitchen_dashboard.php'; break;
                    case 'cashier': $dashboard_link = 'cashier_dashboard.php'; break;
                    case 'admin': $dashboard_link = 'admin_dashboard.php'; break;
                }
            ?>
            <a href="<?php echo $dashboard_link; ?>" class="btn-link">Go to Your Dashboard</a>
            <a href="logout.php" class="btn-link-secondary">Logout</a>

        <?php else: ?>
            
            <p>Staff members, please log in to access your dashboard.</p>
            <a href="login.php" class="btn-link">Staff Login</a>
            
        <?php endif; ?>
        
    </div>

    <!-- === NEW: JavaScript to load announcements === -->
    <script>
        async function loadAnnouncements() {
            try {
                const response = await fetch('api/get_announcements.php');
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                const box1 = document.getElementById('announcement-box1-text');
                const box2 = document.getElementById('announcement-box2-text');

                if (data.box1) {
                    box1.textContent = data.box1;
                } else {
                    box1.textContent = "Welcome!";
                }

                if (data.box2) {
                    box2.textContent = data.box2;
                } else {
                    box2.textContent = "Check out our daily specials.";
                }

            } catch (error) {
                console.error('Error loading announcements:', error);
                // Hide boxes if there's an error
                document.querySelectorAll('.announcement-box').forEach(box => box.style.display = 'none');
            }
        }

        // Load them when the page opens
        document.addEventListener('DOMContentLoaded', loadAnnouncements);
    </script>
</body>
</html>

