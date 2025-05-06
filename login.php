<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // A1:2017 - Injection vulnerability - Direct SQL query without prepared statements
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // A3:2017 - Sensitive Data Exposure - Storing sensitive data in session
        $_SESSION['password'] = $password;
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials";
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
</div>

<?php require_once 'footer.php'; ?> 