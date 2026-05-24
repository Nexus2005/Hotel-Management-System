<?php
require_once 'config/db.php';
$hash = password_hash('Admin@123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = 'admin'");
$stmt->execute([$hash]);
echo "Password updated.";
?>
