<?php
// archive-handler.php (or wherever archive functions are defined)
require_once __DIR__ . "/../db.php";

function archiveRecord($pdo, $table, $id, $idColumn)
{
    try {
        $isNewTransaction = !$pdo->inTransaction(); // Check if no transaction is active
        if ($isNewTransaction) {
            $pdo->beginTransaction(); // Start transaction only if none is active
        }

        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $idColumn = :id");
        $stmt->execute(['id' => $id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) throw new Exception("Record not found in $table");
        $data = json_encode($record);
        $pdo->prepare("INSERT INTO archive (original_table, original_id, data, deleted_at) VALUES (?, ?, ?, NOW())")
            ->execute([$table, $id, $data]);
        $pdo->prepare("DELETE FROM $table WHERE $idColumn = ?")->execute([$id]);

        if ($isNewTransaction) {
            $pdo->commit(); // Commit only if we started the transaction
        }
        return true;
    } catch (Exception $e) {
        if ($isNewTransaction && $pdo->inTransaction()) {
            $pdo->rollBack(); // Roll back only if we started the transaction
        }
        throw new Exception("Archive failed: " . $e->getMessage());
    }
}

// Include other functions (restoreRecord, deleteFromArchive) with similar transaction handling if needed
function restoreRecord($pdo, $id)
{
    try {
        $client_id = $id;

        // Fetch client name before update
        $stmt = $pdo->prepare("SELECT client_name FROM client WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        $client_name = $client['client_name'] ?? 'Unknown';

        // Begin transaction
        $pdo->beginTransaction();

        // ✅ Fix: Use client_id to archive all their pets
        $stmt = $pdo->prepare("UPDATE pet SET status = 1 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Archive the client
        $stmt = $pdo->prepare("UPDATE client SET status = 1 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Log the action
        $actionType = 'restore';
        $description = $_SESSION['username'] . " restored client '$client_name'";
        logAction($pdo, $_SESSION['vet_id'], $actionType, $description, 'Admin');

        // Commit transaction
        $pdo->commit();

        header('Location: clients.php?message=Client and associated pets restored successfully');
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Database error: Cannot restore client and pets. " . $e->getMessage();
    }
}

function deleteFromArchive($pdo, $archiveId)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM archive WHERE id = ?");
        $stmt->execute([$archiveId]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        throw new Exception("Delete failed: " . $e->getMessage());
    }
}
