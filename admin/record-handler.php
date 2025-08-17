<?php
require_once __DIR__ . "/../db.php"; // Ensure this includes your PDO connection

function getMedicalRecords($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE status = 1 ORDER BY record_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        global $error;
        $error = "Database error: " . $e->getMessage();
        return [];
    }
}

function getVeterinarians($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM Veterinarian ORDER BY vet_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        global $error;
        $error = "Database error: " . $e->getMessage();
        return [];
    }
}

function getPets($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM Pet WHERE status = 1 ORDER BY pet_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        global $error;
        $error = "Database error: " . $e->getMessage();
        return [];
    }
}
