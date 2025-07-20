<?php
// archive-handler.php (or wherever archive functions are defined)
require_once './db.php';

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
function restoreRecord($pdo, $table, $archiveId, $idColumn)
{
    try {
        $isNewTransaction = !$pdo->inTransaction();
        if ($isNewTransaction) {
            $pdo->beginTransaction();
        }

        $stmt = $pdo->prepare("SELECT * FROM archive WHERE id = ? AND original_table = ?");
        $stmt->execute([$archiveId, $table]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) throw new Exception("Archived record not found");
        $data = json_decode($record['data'], true);
        if (!$data) throw new Exception("Data decode failed");
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)")->execute(array_values($data));
        $pdo->prepare("DELETE FROM archive WHERE id = ?")->execute([$archiveId]);

        if ($isNewTransaction) {
            $pdo->commit();
        }
        return true;
    } catch (Exception $e) {
        if ($isNewTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw new Exception("Restore failed: " . $e->getMessage());
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
