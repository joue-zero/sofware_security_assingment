<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// FIX: Use prepared statements
$stmt = $conn->prepare(
    "SELECT t.*, 
        (SELECT account_number FROM accounts WHERE account_number = t.from_account) as from_acc,
        (SELECT account_number FROM accounts WHERE account_number = t.to_account) as to_acc
    FROM transactions t
    WHERE t.from_account IN (SELECT account_number FROM accounts WHERE user_id = ?)
    OR t.to_account IN (SELECT account_number FROM accounts WHERE user_id = ?)
    ORDER BY t.created_at DESC"
);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$transactions = $stmt->get_result();

require_once 'header.php';
?>

<div class="card">
    <h2>Transaction History</h2>
    
    <table>
        <tr>
            <th>Date</th>
            <th>From Account</th>
            <th>To Account</th>
            <th>Amount</th>
            <th>Description</th>
        </tr>
        <?php while ($transaction = $transactions->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
            <td><?php echo htmlspecialchars($transaction['from_acc']); ?></td>
            <td><?php echo htmlspecialchars($transaction['to_acc']); ?></td>
            <td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>
            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require_once 'footer.php'; ?> 