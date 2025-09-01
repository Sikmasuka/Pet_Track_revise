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

// Count total logs
$totalStmt = $pdo->query("SELECT COUNT(*) FROM Logs WHERE Table_Affected IN ('Guest', 'veterinarian')");
$totalLogs = $totalStmt->fetchColumn();
$totalPages = ceil($totalLogs / $itemsPerPage);

// Fetch paginated logs
$logQuery = "
    SELECT 
        l.Description,
        l.Timestamp,
        ap.owner_name AS name
    FROM Logs l
    LEFT JOIN appointments ap 
        ON ap.id = (
            SELECT MAX(id) 
            FROM appointments 
            WHERE owner_name LIKE CONCAT('%', SUBSTRING_INDEX(l.Description, ' ', 2), '%')
        )
    WHERE l.Table_Affected IN ('Guest', 'veterinarian')
    ORDER BY l.Timestamp DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($logQuery);
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
