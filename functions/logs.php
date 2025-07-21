<?php
require_once __DIR__ . "/../db.php";

function logAction($pdo, $userId, $actionType, $description, $userRole)
{
    $stmt = $pdo->prepare("INSERT INTO Logs (User_ID, Action_Type, Description, Table_Affected) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $actionType, $description, $userRole]); // Use Table_Affected to store the role: 'Admin' or 'Veterinarian'
}
