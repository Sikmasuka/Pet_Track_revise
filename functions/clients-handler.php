<?php
// Start session and include database connection
session_start();
require_once 'db.php';
require_once 'functions/archive-handler.php';
require_once './functions/logs.php';

// Check if user is logged in
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch vet name for greeting
$stmt = $pdo->prepare("SELECT vet_name FROM Veterinarian WHERE vet_id=?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch();
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";

/**
 * Input validation function
 */
function validateInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Handle adding and updating clients via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clients
    $client_name = validateInput($_POST['client_name'] ?? '');
    $client_address = validateInput($_POST['client_address'] ?? '');
    $client_contact = validateInput($_POST['client_contact_number'] ?? '');

    // Pets
    $pet_name = validateInput($_POST['pet_name'] ?? '');
    $pet_sex = $_POST['pet_sex'] ?? '';
    $pet_weight = $_POST['pet_weight'] ?? '';
    $pet_breed = validateInput($_POST['pet_breed'] ?? '');
    $pet_birth_date = $_POST['pet_birth_date'] ?? '';
    $pet_species = $_POST['pet_species'] ?? '';

    // Medical Records
    $medical_condition = validateInput($_POST['medical_condition'] ?? '');
    $medical_diagnosis = validateInput($_POST['medical_diagnosis'] ?? '');
    $medical_symptoms = validateInput($_POST['medical_symptoms'] ?? '');
    $medical_treatment = validateInput($_POST['medical_treatment'] ?? '');

    // Basic validation for all required fields
    if (empty($client_name) || empty($client_address) || empty($client_contact)) {
        $error = "All client fields are required";
    } elseif (empty($pet_name) || empty($pet_sex) || empty($pet_weight) || empty($pet_breed) || empty($pet_birth_date) || empty($pet_species)) {
        $error = "All pet fields are required";
    } elseif (empty($medical_condition) || empty($medical_diagnosis) || empty($medical_symptoms) || empty($medical_treatment)) {
        $error = "All medical record fields are required";
    } else {
        try {
            if (isset($_POST['add_client'])) {
                $pdo->beginTransaction();
                // Insert Client
                $stmt = $pdo->prepare("INSERT INTO Client (client_name, client_address, client_contact_number) VALUES (?, ?, ?)");
                $stmt->execute([$client_name, $client_address, $client_contact]);
                $client_id = $pdo->lastInsertId();

                // Insert Pet
                $stmt = $pdo->prepare("INSERT INTO Pet (pet_name, pet_sex, pet_weight, pet_breed, pet_birth_date, pet_species, client_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_species, $client_id]);
                $pet_id = $pdo->lastInsertId();

                // Insert Medical Record
                $stmt = $pdo->prepare("INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment, status, record_date) VALUES (?, CURDATE(), ?, ?, ?, ?, ?, CURDATE())");
                $stmt->execute([$pet_id, $medical_condition, $medical_diagnosis, $medical_symptoms, $medical_treatment, 1]);

                // Log action
                $description = $_SESSION['username'] . " added a new client '$client_name', pet '$pet_name', and medical record";
                logAction($pdo, $_SESSION['vet_id'], 'add', $description, 'Admin');
                $pdo->commit();
                header('Location: clients.php?message=Client, pet, and medical record added successfully');
                exit;
            } elseif (isset($_POST['update_client'])) {
                $client_id = (int)$_POST['client_id'];
                $pet_id = (int)$_POST['pet_id'];
                $record_id = !empty($_POST['record_id']) ? (int)$_POST['record_id'] : null;

                // Update client
                $stmt = $pdo->prepare("UPDATE Client SET client_name=?, client_address=?, client_contact_number=? WHERE client_id=?");
                $stmt->execute([$client_name, $client_address, $client_contact, $client_id]);

                // Check if pet fields are provided to update or insert a pet
                $pet_fields_provided = !empty($pet_name) && !empty($pet_sex) && !empty($pet_weight) && !empty($pet_breed) && !empty($pet_birth_date) && !empty($pet_species);
                if ($pet_fields_provided) {
                    // Check if pet exists to update, otherwise insert
                    $stmt = $pdo->prepare("SELECT pet_id FROM Pet WHERE pet_id = ? AND client_id = ?");
                    $stmt->execute([$pet_id, $client_id]);
                    if ($stmt->fetch()) {
                        // Update existing pet
                        $stmt = $pdo->prepare("UPDATE Pet SET pet_name=?, pet_sex=?, pet_weight=?, pet_breed=?, pet_birth_date=?, pet_species=? WHERE pet_id=? AND client_id=?");
                        $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_species, $pet_id, $client_id]);
                    } else {
                        // Insert new pet
                        $stmt = $pdo->prepare("INSERT INTO Pet (pet_name, pet_sex, pet_weight, pet_breed, pet_birth_date, pet_species, client_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_species, $client_id]);
                        $pet_id = $pdo->lastInsertId();
                    }
                    $description = $_SESSION['username'] . " updated client '$client_name' with pet '$pet_name'";
                } else {
                    $description = $_SESSION['username'] . " updated client '$client_name'";
                }

                // Insert or Update Medical Record
                if ($pet_id) {
                    if ($record_id) {
                        // Update existing medical record
                        $stmt = $pdo->prepare("UPDATE Medical_Records SET date=CURDATE(), medical_condition=?, medical_diagnosis=?, medical_symptoms=?, medical_treatment=?, status=1, updated_at=NOW() WHERE record_id=? AND pet_id=?");
                        $stmt->execute([$medical_condition, $medical_diagnosis, $medical_symptoms, $medical_treatment, $record_id, $pet_id]);
                        $description .= " and updated a medical record";
                    } else {
                        // Insert new medical record
                        $stmt = $pdo->prepare("INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment, status, record_date) VALUES (?, CURDATE(), ?, ?, ?, ?, ?, CURDATE())");
                        $stmt->execute([$pet_id, $medical_condition, $medical_diagnosis, $medical_symptoms, $medical_treatment, 1]);
                        $description .= " and added a medical record";
                    }
                } else {
                    $error = "Cannot add or update medical record without a valid pet";
                }

                if (!isset($error)) {
                    logAction($pdo, $_SESSION['vet_id'], 'update', $description, 'Admin');
                    header('Location: clients.php?message=Client' . ($pet_fields_provided ? " and pet" : "") . ' and medical record updated successfully');
                    exit;
                }
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Database error: " . $e->getMessage();
        }
    }
}

/**
 * Handle archiving a client and their pets via GET request
 */
if (isset($_GET['delete_client_id']) && is_numeric($_GET['delete_client_id'])) {
    try {
        $client_id = (int)$_GET['delete_client_id'];

        // Fetch client name before update
        $stmt = $pdo->prepare("SELECT client_name FROM Client WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        $client_name = $client['client_name'] ?? 'Unknown';

        // Begin transaction
        $pdo->beginTransaction();

        // Archive all pets for this client
        $stmt = $pdo->prepare("UPDATE Pet SET status = 0 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Archive the client
        $stmt = $pdo->prepare("UPDATE Client SET status = 0 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Archive associated medical records
        $stmt = $pdo->prepare("UPDATE Medical_Records SET status = 0 WHERE pet_id IN (SELECT pet_id FROM Pet WHERE client_id = ?)");
        $stmt->execute([$client_id]);

        // Log the action
        $actionType = 'delete';
        $description = $_SESSION['username'] . " archived client '$client_name'";
        logAction($pdo, $_SESSION['vet_id'], $actionType, $description, 'Admin');

        // Commit transaction
        $pdo->commit();

        header('Location: clients.php?message=Client, associated pets, and medical records archived successfully');
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Database error: Cannot archive client, pets, and medical records. " . $e->getMessage();
    }
}

/**
 * Fetch client data for editing
 */
function getDataToEdit($pdo)
{
    $clientToEdit = null;
    $petToEdit = null;
    $medicalRecordToEdit = null;
    $error = null;

    if (isset($_GET['edit_client_id']) && is_numeric($_GET['edit_client_id'])) {
        try {
            // Get client info
            $stmt = $pdo->prepare("SELECT * FROM Client WHERE client_id = ? AND status = 1");
            $stmt->execute([(int)$_GET['edit_client_id']]);
            $clientToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get the first pet for this client
            if ($clientToEdit) {
                $stmt = $pdo->prepare("SELECT * FROM Pet WHERE client_id = ? AND status = 1 LIMIT 1");
                $stmt->execute([(int)$_GET['edit_client_id']]);
                $petToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

                // Get the first medical record for this pet (if exists)
                if ($petToEdit) {
                    $stmt = $pdo->prepare("SELECT * FROM Medical_Records WHERE pet_id = ? AND status = 1 LIMIT 1");
                    $stmt->execute([$petToEdit['pet_id']]);
                    $medicalRecordToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }

    return [
        'client' => $clientToEdit,
        'pet' => $petToEdit,
        'medical_record' => $medicalRecordToEdit,
        'error' => $error
    ];
}

// Get data for editing
$editData = getDataToEdit($pdo);
$clientToEdit = $editData['client'];
$petToEdit = $editData['pet'];
$medicalRecordToEdit = $editData['medical_record'];
$error = $error ?? $editData['error'];

/**
 * Fetch all clients
 */
try {
    $stmt = $pdo->prepare("SELECT * FROM Client WHERE status = 1 ORDER BY client_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $clients = [];
}
