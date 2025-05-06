<?php
session_start();
require_once 'config.php';
require_once 'log_action.php';

// A7:2017 - Cross-Site Scripting (XSS) vulnerability - No input sanitization
$username = $_SESSION['username'];

// A5:2017 - Broken Access Control - No proper session validation
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user accounts
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$accounts = $stmt->get_result();

// Handle transfer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transfer'])) {
    // FIX: CSRF token check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    $from_account = $_POST['from_account'];
    $to_account = $_POST['to_account'];
    $amount = $_POST['amount'];
    
    // FIX: Use prepared statements for all queries
    $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_number = ?");
    $stmt->bind_param("ds", $amount, $from_account);
    $stmt->execute();
    
    $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_number = ?");
    $stmt->bind_param("ds", $amount, $to_account);
    $stmt->execute();
    
    $stmt = $conn->prepare("INSERT INTO transactions (from_account, to_account, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $from_account, $to_account, $amount);
    $stmt->execute();
    
    log_action($conn, $_SESSION['user_id'], $_SESSION['username'], "Transferred $amount from $from_account to $to_account");
    
    $success = "Transfer successful!";
}

require_once 'header.php';
?>

<div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    
    <h3>Your Accounts</h3>
    <table>
        <tr>
            <th>Account Number</th>
            <th>Balance</th>
        </tr>
        <?php while ($account = $accounts->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($account['account_number']); ?></td>
            <td>$<?php echo htmlspecialchars($account['balance']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<div class="card">
    <h3>Transfer Money</h3>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
        <input type="hidden" name="transfer" value="1">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <input type="text" name="from_account" placeholder="From Account" required>
        </div>
        <div class="form-group">
            <input type="text" name="to_account" placeholder="To Account" required>
        </div>
        <div class="form-group">
            <input type="number" name="amount" placeholder="Amount" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Transfer">
        </div>
    </form>
</div>

<div class="card">
    <h3>View Statement</h3>
    <a href="statement.php">View Transaction History</a>
</div>

<div class="card">
    <h3>File Complaint</h3>
    <a href="complaint.php">Submit Complaint</a>
</div>

<div class="card">
    <h3>Message Admin</h3>
    <a href="message.php">Send Message</a>
</div>

<?php require_once 'footer.php'; ?> 