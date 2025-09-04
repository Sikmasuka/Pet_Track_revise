<?php

/**
 * Function: deleteFromArchive
 *
 * Purpose:
 *   Permanently deletes either:
 *     - a client (and all their pets + medical records), OR
 *     - a medical record (linked to a specific pet).
 *   Also logs the action with the vet’s username for auditing.
 *
 * Parameters:
 *   - PDO    $pdo   : Active database connection object.
 *   - int    $id    : The record ID (client_id if table=client, record_id if table=medical_records).
 *   - string $table : Which table to target ('client' or 'medical_records').
 *
 * Returns:
 *   - bool: true if the delete succeeded.
 *   - Throws Exception if something goes wrong.
 *
 * Side-effects:
 *   - Deletes rows from multiple related tables (Client, Pet, Medical_Records).
 *   - Inserts a new row in your action log via logAction().
 *   - Uses transactions: if any step fails, changes are rolled back (undone).
 *
 * Usage Example:
 *   <?php
 *     require_once 'C230196_backend.php';
 *     try {
 *         $ok = deleteFromArchive($pdo, 5, 'client');
 *         if ($ok) {
 *             echo "Client deleted successfully!";
 *         }
 *     } catch (Exception $e) {
 *         echo "Error: " . $e->getMessage();
 *     }
 *   ?>
 */

function deleteFromArchive($pdo, $id, $table)
{
    try {
        if ($table === 'client') {

            // Fetch client name for logging
            $stmt = $pdo->prepare("SELECT client_name FROM client WHERE client_id = ?");
            $stmt->execute([$id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            $client_name = $client ? htmlspecialchars($client['client_name']) : 'Unknown';

            // Fetch vet username for logging
            $stmt = $pdo->prepare("SELECT vet_username FROM veterinarian WHERE vet_id = ?");
            $stmt->execute([$_SESSION['vet_id']]);
            $vet = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $vet ? htmlspecialchars($vet['vet_username']) : 'Unknown';

            // Begin transaction (safe box for DB changes)
            $pdo->beginTransaction();

            // Delete medical records linked to client's pets
            $pdo->prepare("DELETE FROM Medical_Records WHERE pet_id IN (SELECT pet_id FROM Pet WHERE client_id = ?)")
                ->execute([$id]);

            // Delete client's pets
            $pdo->prepare("DELETE FROM Pet WHERE client_id = ?")->execute([$id]);

            // Delete client
            $pdo->prepare("DELETE FROM client WHERE client_id = ?")->execute([$id]);

            // Log the delete action
            $description = "$username permanently deleted client '$client_name' and associated pets and medical records";
            logAction($pdo, $_SESSION['vet_id'], 'delete', $description, 'Admin');

            // Save all changes
            $pdo->commit();
        } elseif ($table === 'medical_records') {
            // Fetch record and pet details for logging
            $stmt = $pdo->prepare("SELECT mr.record_id, p.pet_name 
                                  FROM Medical_Records mr 
                                  JOIN Pet p ON mr.pet_id = p.pet_id 
                                  WHERE mr.record_id = ?");
            $stmt->execute([$id]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            $pet_name = $record ? htmlspecialchars($record['pet_name']) : 'Unknown';

            // Fetch vet username for logging
            $stmt = $pdo->prepare("SELECT vet_username FROM veterinarian WHERE vet_id = ?");
            $stmt->execute([$_SESSION['vet_id']]);
            $vet = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $vet ? htmlspecialchars($vet['vet_username']) : 'Unknown';

            // Delete medical record
            $pdo->prepare("DELETE FROM Medical_Records WHERE record_id = ?")->execute([$id]);

            // Log the delete action
            $description = "$username permanently deleted medical record ID $id for pet '$pet_name'";
            logAction($pdo, $_SESSION['vet_id'], 'delete', $description, 'Admin');
        } else {
            throw new Exception("Invalid table: $table");
        }
        return true;
    } catch (Exception $e) {
        // If something fails, undo changes
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw new Exception("Delete failed: " . $e->getMessage());
    }
}
