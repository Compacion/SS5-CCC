<header class="header">
    <nav class="navbar">
        <div class="logo">
            <h1>🚗 Car Wash Pro</h1>
        </div>
        
        <button class="menu-toggle">☰</button>
        
        <ul class="nav-menu">
            <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a></li>
            <li><a href="appointments.php" <?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'class="active"' : ''; ?>>Appointments</a></li>
            <li><a href="customers.php" <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'class="active"' : ''; ?>>Customers</a></li>
            <li><a href="services.php" <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'class="active"' : ''; ?>>Services</a></li>
            <li><a href="transactions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'class="active"' : ''; ?>>Transactions</a></li>
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                <li><a href="staff.php" <?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'class="active"' : ''; ?>>Staff</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
</header>
