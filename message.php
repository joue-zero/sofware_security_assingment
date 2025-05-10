<?php
session_start();
require_once 'config.php';
require_once 'log_action.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];
    // A7:2017 - Cross-Site Scripting (XSS) - No input sanitization
    $query = "INSERT INTO messages (user_id, message) VALUES ($user_id, '$message')";
    mysqli_query($conn, $query);
    
    log_action($conn, $_SESSION['user_id'], $_SESSION['username'], "Sent a message: $message");
    
    $success = "Message sent successfully!";
}

// Get messages
$query = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC";
$messages = mysqli_query($conn, $query);

require_once 'header.php';
?>

<div class="card">
    <h2>Message Admin</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <textarea name="message" placeholder="Type your message" required></textarea>
        </div>
        <div class="form-group">
            <input type="submit" value="Send Message">
        </div>
    </form>
</div>

<div class="card">
    <h3>Message History</h3>
    <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
    <div class="message">
        <strong><?php echo $msg['username']; ?></strong> (<?php echo $msg['created_at']; ?>):<br>
        <?php echo $msg['message']; ?>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'footer.php'; ?> 