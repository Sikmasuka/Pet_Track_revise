<?php
require_once './db.php';
require_once 'auth.php'; // Include auth.php

// Protect vet pages
requireVet();


// Check login
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch current vet data for the modal
$stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";

// Debug: Check if data is fetched
if (!$vetName) {
    echo "<script>console.error('No vet data found for vet_id: " . $_SESSION['vet_id'] . "');</script>";
} else {
    echo "<script>console.log('Vet data loaded:', " . json_encode($vetName) . ");</script>";
}

// Fetch the counts
$stmtClients = $pdo->prepare("SELECT COUNT(*) FROM Client");
$stmtClients->execute();
$clientCount = $stmtClients->fetchColumn();

$stmtPets = $pdo->prepare("SELECT COUNT(*) FROM Pet");
$stmtPets->execute();
$petCount = $stmtPets->fetchColumn();

$stmtRecords = $pdo->prepare("SELECT COUNT(*) FROM Medical_Records");
$stmtRecords->execute();
$recordCount = $stmtRecords->fetchColumn();

$today = date('Y-m-d');

$stmtAppointments = $pdo->prepare("
    SELECT COUNT(*) 
    FROM Appointments 
    WHERE DATE(created_at) = :today
");
$stmtAppointments->execute(['today' => $today]);
$appointmentsToday = $stmtAppointments->fetchColumn();



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

// Fetch total payment amount
$stmtPayment = $pdo->prepare("SELECT SUM(amount) FROM Payments");
$stmtPayment->execute();
$totalPayment = $stmtPayment->fetchColumn();
$totalPayment = $totalPayment ? number_format((float) $totalPayment, 2, '.', '') : "0.00";


// Fetch monthly income (grouped by month)s
$stmtMonthly = $pdo->prepare("
SELECT DATE_FORMAT(date, '%b') AS month,
MONTH(date) AS month_num,
SUM(amount) AS total
FROM Payments
GROUP BY month_num
ORDER BY month_num
");
$stmtMonthly->execute();
$monthlyData = $stmtMonthly->fetchAll();

// Initialize all 12 months to 0
$allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyTotals = array_fill(0, 12, 0);

// Fill in actual totals from DB
$monthlyLabels = $allMonths;
foreach ($monthlyData as $data) {
    $index = (int)$data['month_num'] - 1;
    $monthlyTotals[$index] = round($data['total'], 2);
}
