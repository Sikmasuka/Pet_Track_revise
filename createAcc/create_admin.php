<?php
require_once __DIR__ . '/../db.php';

$username = "jonggun";
$adminName = "Park Jong Geon";
$password = "Shirooni123!"; // plain password
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO Admin (admin_username, admin_name , admin_password) VALUES (?, ?, ?)");
$stmt->execute([$username, $adminName, $hash]);

echo "✅ Admin created: username = jonggun | password = Shirooni123!";
