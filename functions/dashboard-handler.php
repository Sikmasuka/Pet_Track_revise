<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch vet name for greeting
$stmt = $pdo->prepare("SELECT vet_name FROM Veterinarian WHERE vet_id=?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch();
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";

// Fetch the counts for Clients, Pets, and Medical Records
$stmtClients = $pdo->prepare("SELECT COUNT(*) FROM Client");
$stmtClients->execute();
$clientCount = $stmtClients->fetchColumn();

$stmtPets = $pdo->prepare("SELECT COUNT(*) FROM Pet");
$stmtPets->execute();
$petCount = $stmtPets->fetchColumn();

$stmtRecords = $pdo->prepare("SELECT COUNT(*) FROM Medical_Records");
$stmtRecords->execute();
$recordCount = $stmtRecords->fetchColumn();

// Fetch most common medical conditions
$stmtConditions = $pdo->prepare("
    SELECT medical_condition, COUNT(*) AS condition_count
    FROM Medical_Records
    GROUP BY medical_condition
    ORDER BY condition_count DESC
    LIMIT 5
");
$stmtConditions->execute();
$conditions = $stmtConditions->fetchAll();
$conditionLabels = [];
$conditionCounts = [];

foreach ($conditions as $condition) {
    $conditionLabels[] = htmlspecialchars($condition['medical_condition']);
    $conditionCounts[] = $condition['condition_count'];
}
