<?php
// dashboard-handler.php: Handles data fetching and preparation for the veterinarian dashboard page.
// This script retrieves various statistics and data points needed to display on the dashboard,
// including user info, counts of clients/pets/records, medical conditions, payments, and monthly income.

// Include database connection and authentication functions
require_once './db.php';
require_once 'auth.php'; // Include auth.php

// Protect vet pages: Ensure only logged-in veterinarians can access this page
requireVet();

// Check login: Redirect to index if vet_id is not set in session
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch current vet data for the modal: Get the veterinarian's name for display
$stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";

// Debug: Check if data is fetched and log to browser console
if (!$vetName) {
    echo "<script>console.error('No vet data found for vet_id: " . $_SESSION['vet_id'] . "');</script>";
} else {
    echo "<script>console.log('Vet data loaded:', " . json_encode($vetName) . ");</script>";
}

// Fetch the counts: Get total number of clients, pets, medical records, and today's appointments
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

// Fetch most common medical conditions: Retrieve top 5 medical conditions by frequency
$stmtConditions = $pdo->prepare("
    SELECT LOWER(TRIM(medical_condition)) AS `condition_name`, COUNT(*) AS condition_count
    FROM Medical_Records
    GROUP BY `condition_name`
    ORDER BY condition_count DESC
    LIMIT 5
");
$stmtConditions->execute();
$conditions = $stmtConditions->fetchAll();

$conditionLabels = [];
$conditionCounts = [];

foreach ($conditions as $condition) {
    // I-format balik para presentable
    $conditionLabels[] = ucwords($condition['condition_name']);
    $conditionCounts[] = $condition['condition_count'];
}


// Fetch total payment amount: Calculate the sum of all payments
$stmtPayment = $pdo->prepare("SELECT SUM(amount) FROM Payments");
$stmtPayment->execute();
$totalPayment = $stmtPayment->fetchColumn();
$totalPayment = $totalPayment ? number_format((float) $totalPayment, 2, '.', '') : "0.00";

// Fetch monthly income (grouped by month): Get total payments per month for the year
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

// Initialize all 12 months to 0: Prepare arrays for monthly data with default zero values
$allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyTotals = array_fill(0, 12, 0);

// Fill in actual totals from DB: Populate the monthly totals array with database values
$monthlyLabels = $allMonths;
foreach ($monthlyData as $data) {
    $index = (int)$data['month_num'] - 1;
    $monthlyTotals[$index] = round($data['total'], 2);
}
