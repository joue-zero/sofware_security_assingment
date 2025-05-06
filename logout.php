<?php
require_once 'config.php';
require_once 'log_action.php';
session_start();
if (isset($_SESSION['user_id'])) {
    log_action($conn, $_SESSION['user_id'], $_SESSION['username'], 'Logged out');
}
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header("Location: login.php");
exit();
?> 