<?php
require_once __DIR__ . '/../db.php';

// Default Admin
$adminUser = "admin";
$adminPass = password_hash("Admin12345!", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT admin_id FROM Admin WHERE admin_username = :username");
$stmt->execute(['username' => $adminUser]);
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO Admin (admin_username, admin_password) VALUES (:username, :password)");
    $stmt->execute(['username' => $adminUser, 'password' => $adminPass]);
    echo "✅ Default Admin account created (username: admin / password: Admin12345!)<br>";
} else {
    echo "⚠️ Admin account already exists.<br>";
}

// Default Veterinarian
$vetUser = "vet";
$vetPass = password_hash("Vet12345!", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT vet_id FROM Veterinarian WHERE vet_username = :username");
$stmt->execute(['username' => $vetUser]);
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO Veterinarian (vet_username, vet_password, vet_name) 
                           VALUES (:username, :password, 'Default Vet')");
    $stmt->execute(['username' => $vetUser, 'password' => $vetPass]);
    echo "✅ Default Veterinarian account created (username: vet / password: Vet12345!)<br>";
} else {
    echo "⚠️ Veterinarian account already exists.<br>";
}
