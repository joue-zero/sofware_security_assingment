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
    $description = $_POST['description'];
    
    // A4:2017 - XML External Entities (XXE) - No file type validation
    if (isset($_FILES['complaint_file'])) {
        $file_name = $_FILES['complaint_file']['name'];
        $file_tmp = $_FILES['complaint_file']['tmp_name'];
        $upload_dir = "uploads/";
        
        // A6:2017 - Security Misconfiguration - No file type restrictions
        move_uploaded_file($file_tmp, $upload_dir . $file_name);
        
        $query = "INSERT INTO complaints (user_id, file_name, file_path, description) 
                 VALUES ($user_id, '$file_name', '$upload_dir$file_name', '$description')";
        mysqli_query($conn, $query);
        
        $success = "Complaint submitted successfully!";
        log_action($conn, $_SESSION['user_id'], $_SESSION['username'], "Filed a complaint: $description");
    }
}

require_once 'header.php';
?>

<div class="card">
    <h2>File a Complaint</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <form method="POST" enctype="multipart/form-data">
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