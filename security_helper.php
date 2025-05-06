<?php
class SecurityHelper {
    private $conn;
    private $max_attempts = 5; // Maximum failed attempts before lockout
    private $lockout_time = 900; // Lockout duration in seconds (15 minutes)
    private $attempt_window = 300; // Time window for counting attempts (5 minutes)

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function checkBruteForce($username, $ip_address) {
        // Check if account is locked
        $stmt = $this->conn->prepare("SELECT locked_until FROM account_lockouts WHERE username = ? AND locked_until > NOW()");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $locked_until = strtotime($row['locked_until']);
            $time_left = $locked_until - time();
            throw new Exception("Account is locked. Please try again in " . ceil($time_left / 60) . " minutes.");
        }

        // Count recent failed attempts
        $stmt = $this->conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts 
            WHERE username = ? AND success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->bind_param("si", $username, $this->attempt_window);
        $stmt->execute();
        $result = $stmt->get_result();
        $attempts = $result->fetch_assoc()['attempts'];

        if ($attempts >= $this->max_attempts) {
            // Lock the account
            $stmt = $this->conn->prepare("INSERT INTO account_lockouts (username, locked_until) 
                VALUES (?, DATE_ADD(NOW(), INTERVAL ? SECOND))");
            $stmt->bind_param("si", $username, $this->lockout_time);
            $stmt->execute();

            throw new Exception("Too many failed attempts. Account locked for 15 minutes.");
        }
    }

    public function logLoginAttempt($username, $ip_address, $success) {
        $stmt = $this->conn->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $ip_address, $success);
        $stmt->execute();

        if (!$success) {
            // Check and apply rate limiting after logging the attempt
            $this->checkBruteForce($username, $ip_address);
        } else {
            // On successful login, clear any existing lockouts
            $stmt = $this->conn->prepare("DELETE FROM account_lockouts WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
        }
    }

    public function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 12) {
            $errors[] = "Password must be at least 12 characters long";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Password must contain at least one number";
        }
        if (!preg_match("/[^A-Za-z0-9]/", $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(". ", $errors));
        }
        
        return true;
    }
} 