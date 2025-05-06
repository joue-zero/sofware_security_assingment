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
    $description = $_POST['description'];

    // FIX: File type and size validation
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    if (isset($_FILES['complaint_file']) && in_array($_FILES['complaint_file']['type'], $allowed_types) && $_FILES['complaint_file']['size'] < 2*1024*1024) {
        $file_name = $_FILES['complaint_file']['name'];
        $file_tmp = $_FILES['complaint_file']['tmp_name'];
        $upload_dir = "uploads/";
        $safe_name = uniqid() . '_' . basename($file_name); // FIX: Prevent path traversal
        move_uploaded_file($file_tmp, $upload_dir . $safe_name);

        $stmt = $conn->prepare("INSERT INTO complaints (user_id, file_name, file_path, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $safe_name, $upload_dir . $safe_name, $description);
        $stmt->execute();

        $success = "Complaint submitted successfully!";
        log_action($conn, $_SESSION['user_id'], $_SESSION['username'], "Filed a complaint: $description");
    } else {
        $success = "Invalid file type or size.";
    }
}

require_once 'header.php';
?>

<div class="card">
    <h2>File a Complaint</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <textarea name="description" placeholder="Describe your complaint" required></textarea>
        </div>
        <div class="form-group">
            <input type="file" name="complaint_file" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Submit Complaint">
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?> 