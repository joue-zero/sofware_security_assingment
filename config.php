<?php
// A1:2017 - Injection vulnerability - Hardcoded credentials
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234"; // Hardcoded password
$db_name = "vulnerable_bank";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?> 