<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/auth.php"; // Assuming requireVet() is defined here

requireVet();

// Pagination settings
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Count total logs (vet actions + guest actions)
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM Logs WHERE User_ID = ? OR Table_Affected = 'Guest'");
$totalStmt->execute([$_SESSION['vet_id']]);
$totalLogs = $totalStmt->fetchColumn();
$totalPages = ceil($totalLogs / $itemsPerPage);

// Fetch paginated logs
$logQuery = "
    SELECT 
        l.Description,
        l.Timestamp,
        COALESCE(v.vet_name, 'Guest') AS name
    FROM Logs l
    LEFT JOIN veterinarian v ON l.User_ID = v.vet_id
    WHERE l.User_ID = :vet_id OR l.Table_Affected = 'Guest'
    ORDER BY l.Timestamp DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($logQuery);
$stmt->bindValue(':vet_id', $_SESSION['vet_id'], PDO::PARAM_INT);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Send JSON
header('Content-Type: application/json');
echo json_encode([
    "activities" => $recentActivities,
    "totalPages" => $totalPages,
    "currentPage" => $currentPage,
    "offset" => $offset
]);
ob_end_flush();
exit;
