<?php
session_start();
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$manager = new CarWashManager();
$stats = $manager->getDashboardStats();
$todayAppointments = $manager->getAppointments(date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Car Wash Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Dashboard Overview</h2>
            
            <div class="stats-grid">
                <div class="stat-card appointments">
                    <div class="stat-number"><?php echo $stats['today_appointments']; ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
                
                <div class="stat-card revenue">
                    <div class="stat-number">$<?php echo number_format($stats['today_revenue'], 2); ?></div>
                    <div class="stat-label">Today's Revenue</div>
                </div>
                
                <div class="stat-card customers">
                    <div class="stat-number"><?php echo $stats['total_customers']; ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
                
                <div class="stat-card pending">
                    <div class="stat-number"><?php echo $stats['pending_appointments']; ?></div>
                    <div class="stat-label">Pending Appointments</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Today's Appointments</h2>
            <?php if (empty($todayAppointments)): ?>
                <div class="alert alert-info">No appointments scheduled for today.</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Staff</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todayAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['staff_name'] ?? 'Unassigned'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $appointment['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($appointment['status'] == 'scheduled'): ?>
                                            <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'in_progress')" 
                                                    class="btn btn-sm btn-warning">Start</button>
                                        <?php elseif ($appointment['status'] == 'in_progress'): ?>
                                            <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'completed')" 
                                                    class="btn btn-sm btn-success">Complete</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
