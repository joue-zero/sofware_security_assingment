<?php
session_start();
require_once 'config.php';
require_once 'log_action.php';
require_once 'security_helper.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$security = new SecurityHelper($conn);
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Check for brute force attempts before processing login
        $security->checkBruteForce($username, $ip_address);

        // Validate login
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Log successful attempt
                $security->logLoginAttempt($username, $ip_address, true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                log_action($conn, $user['id'], $user['username'], 'Logged in');
                header("Location: dashboard.php");
                exit();
            } else {
                // Log failed attempt
                $security->logLoginAttempt($username, $ip_address, false);
                $error = "Invalid credentials";
            }
        } else {
            // Log failed attempt
            $security->logLoginAttempt($username, $ip_address, false);
            $error = "Invalid credentials";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="card">
    <h2>Bank Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Login">
        </div>
    </form>
    
    <div style="margin-top: 1rem;">
        <p><strong>Password Requirements:</strong></p>
        <ul>
            <li>At least 12 characters long</li>
            <li>At least one uppercase letter</li>
            <li>At least one lowercase letter</li>
            <li>At least one number</li>
            <li>At least one special character</li>
        </ul>
    </div>
</div>

<?php require_once 'footer.php'; ?> 