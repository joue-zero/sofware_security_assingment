<?php
require_once 'header.php';
?>

<div class="card">
    <h1>Welcome to Vulnerable Bank</h1>
    <p>This is a deliberately vulnerable banking application that demonstrates OWASP Top 10 vulnerabilities.</p>
    <p>Please note that this application is for educational purposes only and should not be used in a production environment.</p>
    
    <h2>Available Accounts</h2>
    <p>You can use the following test accounts:</p>
    <ul>
        <li>Admin: username: admin, password: admin123</li>
        <li>User 1: username: user1, password: password1</li>
        <li>User 2: username: user2, password: password2</li>
        <li>User 3: username: user3, password: password3</li>
    </ul>
    
    <h2>Features</h2>
    <ul>
        <li>Account Management</li>
        <li>Money Transfer</li>
        <li>Transaction History</li>
        <li>File Complaints</li>
        <li>Message Admin</li>
        <li>Admin Dashboard</li>
    </ul>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="login.php" style="display: inline-block; padding: 1rem 2rem; background-color: #333; color: white; text-decoration: none; border-radius: 4px;">Login to Start</a>
    </div>
</div>

<?php require_once 'footer.php'; ?> 