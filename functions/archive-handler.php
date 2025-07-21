<?php
require_once __DIR__ . "/../db.php";

function archiveRecord($pdo, $table, $id, $idColumn)
{
    try {
        // Update status to 0 (archived)
        $pdo->prepare("UPDATE $table SET status = 0, updated_at = NOW() WHERE $idColumn = ?")->execute([$id]);

        // Archive pets if client
        if ($table === 'client') {
            $pdo->prepare("UPDATE pet SET status = 0, updated_at = NOW() WHERE client_id = ?")->execute([$id]);
        }
        return true;
    } catch (Exception $e) {
        throw new Exception("Archive failed: " . $e->getMessage());
    }
}

function restoreRecord($pdo, $id, $table)
{
    try {
        if ($table === 'client') {
            // Restore client and pets (status = 1)
            $pdo->prepare("UPDATE client SET status = 1, updated_at = NOW() WHERE client_id = ?")->execute([$id]);
            $pdo->prepare("UPDATE pet SET status = 1, updated_at = NOW() WHERE client_id = ?")->execute([$id]);
        } elseif ($table === 'medical_records') {
            // Restore medical record (status = 1)
            $pdo->prepare("UPDATE medical_records SET status = 1, updated_at = NOW() WHERE record_id = ?")->execute([$id]);
        } else {
            throw new Exception("Invalid table: $table");
        }
        return true;
    } catch (Exception $e) {
        throw new Exception("Restore failed: " . $e->getMessage());
    }
}

function deleteFromArchive($pdo, $id, $table)
{
    try {
        if ($table === 'client') {
            // Delete client and their pets
            $pdo->prepare("DELETE FROM client WHERE client_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM pet WHERE client_id = ?")->execute([$id]);
        } elseif ($table === 'medical_records') {
            // Delete medical record
            $pdo->prepare("DELETE FROM medical_records WHERE record_id = ?")->execute([$id]);
        } else {
            throw new Exception("Invalid table: $table");
        }
        return true;
    } catch (Exception $e) {
        throw new Exception("Delete failed: " . $e->getMessage());
    }
}
