<?php
session_start();
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$manager = new CarWashManager();
$message = '';

// Handle form submission for adding transaction
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'add_transaction') {
    $result = $manager->addTransaction(
        $_POST['appointment_id'],
        $_POST['amount'],
        $_POST['payment_method'],
        $_POST['notes'] ?? ''
    );
    
    $message = $result ? 
        '<div class="alert alert-success">Transaction recorded successfully!</div>' :
        '<div class="alert alert-error">Failed to record transaction.</div>';
}

// Get completed appointments that don't have transactions yet
$completedAppointments = $manager->getAppointments();
$completedAppointments = array_filter($completedAppointments, function($apt) {
    return $apt['status'] == 'completed';
});

$transactions = $manager->getTransactions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Car Wash Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php echo $message; ?>
        
        <div class="card">
            <h2>Record New Transaction</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_transaction">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="appointment_id">Completed Service</label>
                        <select id="appointment_id" name="appointment_id" class="form-control" required onchange="updateAmount()">
                            <option value="">Select Completed Service</option>
                            <?php foreach ($completedAppointments as $appointment): ?>
                                <option value="<?php echo $appointment['id']; ?>" 
                                        data-price="<?php echo $appointment['service_price']; ?>">
                                    <?php echo htmlspecialchars($appointment['customer_name']) . ' - ' . 
                                              htmlspecialchars($appointment['service_name']) . ' - ' .
                                              date('M d, Y', strtotime($appointment['appointment_date'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" id="amount" name="amount" class="form-control" 
                               min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="digital_wallet">Digital Wallet</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" 
                                  placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn">Record Transaction</button>
            </form>
        </div>

        <div class="card">
            <h2>Transaction History</h2>
            <div class="table-container">
                <table id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M d, Y g:i A', strtotime($transaction['transaction_date'])); ?></td>
                                <td><?php echo htmlspecialchars($transaction['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['service_name']); ?></td>
                                <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $transaction['payment_status']; ?>">
                                        <?php echo ucfirst($transaction['payment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($transaction['notes'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        function updateAmount() {
            const select = document.getElementById('appointment_id');
            const amountInput = document.getElementById('amount');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                amountInput.value = price;
            } else {
                amountInput.value = '';
            }
        }
    </script>
</body>
</html>
