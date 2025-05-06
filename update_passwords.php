<?php
require_once 'config.php';

// New secure passwords for each user
$new_passwords = [
    'admin' => 'Admin@123456789',
    'user1' => 'User1@123456789',
    'user2' => 'User2@123456789',
    'user3' => 'User3@123456789'
];

foreach ($new_passwords as $username => $new_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashed_password, $username);
    
    if ($stmt->execute()) {
        echo "Updated password for $username<br>";
    } else {
        echo "Failed to update password for $username<br>";
    }
}

echo "<br>Password update complete. New passwords:<br>";
foreach ($new_passwords as $username => $password) {
    echo "$username: $password<br>";
}
?> 