<?php
/**
 * ONE-TIME UTILITY SCRIPT
 * Run this once via browser: http://localhost/Blog-Site-Backend/hash_password.php
 * Then DELETE this file immediately after use.
 */

$password = 'Admin123!';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "<pre>";
echo "Password : " . $password . "\n";
echo "BCrypt Hash : " . $hash . "\n\n";
echo "Run this SQL to update your admin record:\n\n";
echo "UPDATE admin SET password = '" . $hash . "' WHERE username = 'gideon';";
echo "</pre>";
