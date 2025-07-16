<?php
require_once './db.php';

function logAction($conn, $userId, $actionType, $table, $description)
{
    $stmt = $conn->prepare("INSERT INTO Logs (User_ID, Action_Type, Table_Affected, Description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $actionType, $table, $description);
    $stmt->execute();
    $stmt->close();
}
