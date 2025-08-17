<?php
session_start();
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$manager = new CarWashManager();
$message = '';

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'add_customer') {
    $result = $manager->addCustomer(
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address']
    );
    
    $message = $result ? 
        '<div class="alert alert-success">Customer added successfully!</div>' :
        '<div class="alert alert-error">Failed to add customer.</div>';
}

$customers = $manager->getCustomers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Car Wash Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php echo $message; ?>
        
        <div class="card">
            <h2>Add New Customer</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_customer">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn">Add Customer</button>
            </form>
        </div>

        <div class="card">
            <h2>All Customers</h2>
            <div class="table-container">
                <table id="customersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['address'] ?? 'N/A'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
