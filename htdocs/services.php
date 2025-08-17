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
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'add_service') {
    $result = $manager->addService(
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['duration']
    );
    
    $message = $result ? 
        '<div class="alert alert-success">Service added successfully!</div>' :
        '<div class="alert alert-error">Failed to add service.</div>';
}

$services = $manager->getServices();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Car Wash Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php echo $message; ?>
        
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <div class="card">
            <h2>Add New Service</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_service">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" class="form-control" 
                               min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" class="form-control" 
                               min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" 
                                  rows="3" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn">Add Service</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="card">
            <h2>Available Services</h2>
            <div class="table-container">
                <table id="servicesTable">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['name']); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>$<?php echo number_format($service['price'], 2); ?></td>
                                <td><?php echo $service['duration']; ?> mins</td>
                                <td>
                                    <span class="status-badge status-<?php echo $service['status']; ?>">
                                        <?php echo ucfirst($service['status']); ?>
                                    </span>
                                </td>
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
