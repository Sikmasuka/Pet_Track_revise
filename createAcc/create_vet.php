<?php
require_once __DIR__ . '/../db.php';

$username = "vet1";
$vetName = "Dr. Test Vet";
$password = "Vetpass123!";
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO Veterinarian (vet_username, vet_password, vet_name) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, "Dr. Test Vet"]);

echo "✅ Vet created: username = vet1 | password = Vetpass123!";
