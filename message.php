<?php
session_start();
require_once 'config.php';
require_once 'log_action.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // FIX: CSRF token check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    // FIX: Use prepared statements
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();

    log_action($conn, $_SESSION['user_id'], $_SESSION['username'], "Sent a message: $message");
    $success = "Message sent successfully!";
}

$query = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC";
$messages = mysqli_query($conn, $query);

require_once 'header.php';
?>

<div class="card">
    <h2>Message Admin</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
        <strong><?php echo htmlspecialchars($msg['username']); ?></strong> (<?php echo htmlspecialchars($msg['created_at']); ?>):<br>
        <?php echo htmlspecialchars($msg['message']); ?>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'footer.php'; ?> 