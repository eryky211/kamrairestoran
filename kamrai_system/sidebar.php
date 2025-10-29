<?php
// We assume session is already started by auth.php
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? 'User';

// --- Configuration ---
// This $base_path is set in auth.php
// We need it here to make links work from any directory (like /api/)
// If auth.php is not included, we set a default.
if (!isset($base_path)) {
    $base_path = '/kamrai_system/';
}
// ---------------------
?>
<nav class="sidebar">
    <h2 style="color: #F3A683;">Kamrai System</h2>
    
    <div class="sidebar-links">
        <?php // Admin links
        if ($role === 'admin'): ?>
            <a href="<?php echo $base_path; ?>admin_dashboard.php">Dashboard</a>
            <a href="<?php echo $base_path; ?>admin_menu.php">Manage Menu</a>
            <a href="<?php echo $base_path; ?>admin_tables.php">Manage Tables</a>
            <a href="<?php echo $base_path; ?>admin_users.php">Manage Users</a>
            <!-- === NEW LINK ADDED HERE === -->
            <a href="<?php echo $base_path; ?>admin_announcements.php">Manage Announcements</a>
            <a href="<?php echo $base_path; ?>admin_sales.php">Sales Report</a>
            <hr class="sidebar-divider">
        <?php endif; ?>
        
        <?php // Waiter links
        if (in_array($role, ['waiter', 'admin'])): ?>
            <a href="<?php echo $base_path; ?>waiter_dashboard.php">Tables View</a>
        <?php endif; ?>
        
        <?php // Kitchen links
        if (in_array($role, ['kitchen', 'admin'])): ?>
            <a href="<?php echo $base_path; ?>kitchen_dashboard.php">Kitchen View</a>
        <?php endif; ?>

        <?php // Cashier links
        if (in_array($role, ['cashier', 'admin'])): ?>
            <a href="<?php echo $base_path; ?>cashier_dashboard.php">Cashier View</a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <span>Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></span>
        <a href="<?php echo $base_path; ?>logout.php" class="logout-link">Logout</a>
    </div>
</nav>

