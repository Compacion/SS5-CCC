<?php
require_once 'config/database.php';

class CarWashManager {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Authentication functions
    public function login($email, $password) {
        $query = "SELECT id, name, email, password, role FROM staff WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    // Customer functions
    public function addCustomer($name, $email, $phone, $address) {
        $query = "INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $phone, $address]);
    }

    public function getCustomers() {
        $query = "SELECT * FROM customers ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Service functions
    public function getServices() {
        $query = "SELECT * FROM services WHERE status = 'active' ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addService($name, $description, $price, $duration) {
        $query = "INSERT INTO services (name, description, price, duration) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $price, $duration]);
    }

    // Appointment functions
    public function addAppointment($customer_id, $service_id, $staff_id, $appointment_date, $appointment_time, $notes = '') {
        $query = "INSERT INTO appointments (customer_id, service_id, staff_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$customer_id, $service_id, $staff_id, $appointment_date, $appointment_time, $notes]);
    }

    public function getAppointments($date = null) {
        $query = "SELECT a.*, c.name as customer_name, c.phone as customer_phone, 
                         s.name as service_name, s.price as service_price,
                         st.name as staff_name
                  FROM appointments a
                  JOIN customers c ON a.customer_id = c.id
                  JOIN services s ON a.service_id = s.id
                  LEFT JOIN staff st ON a.staff_id = st.id";
        
        if($date) {
            $query .= " WHERE a.appointment_date = ?";
        }
        $query .= " ORDER BY a.appointment_date, a.appointment_time";

        $stmt = $this->conn->prepare($query);
        if($date) {
            $stmt->execute([$date]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAppointmentStatus($id, $status) {
        $query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    // Transaction functions
    public function addTransaction($appointment_id, $amount, $payment_method, $notes = '') {
        $query = "INSERT INTO transactions (appointment_id, amount, payment_method, payment_status, notes) VALUES (?, ?, ?, 'completed', ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$appointment_id, $amount, $payment_method, $notes]);
    }

    public function getTransactions($date = null) {
        $query = "SELECT t.*, a.appointment_date, c.name as customer_name, s.name as service_name
                  FROM transactions t
                  JOIN appointments a ON t.appointment_id = a.id
                  JOIN customers c ON a.customer_id = c.id
                  JOIN services s ON a.service_id = s.id";
        
        if($date) {
            $query .= " WHERE DATE(t.transaction_date) = ?";
        }
        $query .= " ORDER BY t.transaction_date DESC";

        $stmt = $this->conn->prepare($query);
        if($date) {
            $stmt->execute([$date]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dashboard functions
    public function getDashboardStats() {
        $stats = [];
        
        // Today's appointments
        $query = "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['today_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Today's revenue
        $query = "SELECT SUM(amount) as total FROM transactions WHERE DATE(transaction_date) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['today_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Total customers
        $query = "SELECT COUNT(*) as count FROM customers";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Pending appointments
        $query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'scheduled' AND appointment_date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['pending_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }

    public function getStaff() {
        $query = "SELECT id, name, email, role, phone FROM staff ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
