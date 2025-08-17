<?php
session_start();
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$manager = new CarWashManager();
$message = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_appointment':
                $result = $manager->addAppointment(
                    $_POST['customer_id'],
                    $_POST['service_id'],
                    $_POST['staff_id'] ?: null,
                    $_POST['appointment_date'],
                    $_POST['appointment_time'],
                    $_POST['notes'] ?? ''
                );
                $message = $result ? 
                    '<div class="alert alert-success">Appointment scheduled successfully!</div>' :
                    '<div class="alert alert-error">Failed to schedule appointment.</div>';
                break;
                
            case 'update_status':
                $result = $manager->updateAppointmentStatus($_POST['appointment_id'], $_POST['status']);
                $message = $result ? 
                    '<div class="alert alert-success">Appointment status updated!</div>' :
                    '<div class="alert alert-error">Failed to update status.</div>';
                break;
        }
    }
}

$appointments = $manager->getAppointments();
$customers = $manager->getCustomers();
$services = $manager->getServices();
$staff = $manager->getStaff();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Car Wash Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php echo $message; ?>
        
        <div class="card">
            <h2>Schedule New Appointment</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_appointment">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" name="customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>">
                                    <?php echo htmlspecialchars($customer['name']) . ' - ' . htmlspecialchars($customer['phone']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_id">Service</label>
                        <select id="service_id" name="service_id" class="form-control" required>
                            <option value="">Select Service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>">
                                    <?php echo htmlspecialchars($service['name']) . ' - $' . number_format($service['price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="staff_id">Assign Staff</label>
                        <select id="staff_id" name="staff_id" class="form-control">
                            <option value="">Assign Later</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?php echo $member['id']; ?>">
                                    <?php echo htmlspecialchars($member['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment_date">Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" 
                               class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment_time">Time</label>
                        <input type="time" id="appointment_time" name="appointment_time" 
                               class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" 
                                  placeholder="Special instructions or notes..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn">Schedule Appointment</button>
            </form>
        </div>

        <div class="card">
            <h2>All Appointments</h2>
            <div class="table-container">
                <table id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td><?php echo htmlspecialchars($appointment['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['customer_phone']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                <td>$<?php echo number_format($appointment['service_price'], 2); ?></td>
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
                                    
                                    <?php if ($appointment['status'] != 'cancelled'): ?>
                                        <button onclick="updateStatus(<?php echo $appointment['id']; ?>, 'cancelled')" 
                                                class="btn btn-sm btn-danger">Cancel</button>
                                    <?php endif; ?>
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
