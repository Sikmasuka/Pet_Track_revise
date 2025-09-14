<?php
require_once __DIR__ . '/../db.php';

$username = "admin1";
$adminName = "Admin One";
$password = "Adminpass123!"; // plain password
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO Admin (admin_username, admin_name , admin_password) VALUES (?, ?, ?)");
$stmt->execute([$username, $adminName, $hash]);

echo "✅ Admin created: username = admin1 | password = Adminpass123!";
