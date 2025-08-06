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
    $client_name = validateInput($_POST['client_name'] ?? '');
    $client_address = validateInput($_POST['client_address'] ?? '');
    $client_contact = validateInput($_POST['client_contact_number'] ?? '');

    $pet_name = validateInput($_POST['pet_name'] ?? '');
    $pet_sex = $_POST['pet_sex'] ?? '';
    $pet_weight = $_POST['pet_weight'] ?? '';
    $pet_breed = validateInput($_POST['pet_breed'] ?? '');
    $pet_birth_date = $_POST['pet_birth_date'] ?? '';
    $pet_species = $_POST['pet_species'] ?? '';

    // Basic validation
    if (empty($client_name) || empty($client_address) || empty($client_contact)) {
        $error = "All client fields are required";
    } elseif (empty($pet_name) || empty($pet_sex) || empty($pet_weight) || empty($pet_breed) || empty($pet_birth_date) || empty($pet_species)) {
        $error = "All pet fields are required";
    } else {
        try {
            if (isset($_POST['add_client'])) {
                // Begin transaction to ensure atomicity
                $pdo->beginTransaction();

                // Insert new client
                $stmt = $pdo->prepare("INSERT INTO Client (client_name, client_address, client_contact_number) VALUES (?, ?, ?)");
                $stmt->execute([$client_name, $client_address, $client_contact]);

                // Get the last inserted client ID
                $client_id = $pdo->lastInsertId();

                // Insert new pet
                $stmt = $pdo->prepare("INSERT INTO Pet (pet_name, pet_sex, pet_weight, pet_breed, pet_birth_date, pet_species, client_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_species, $client_id]);

                // Log the add action
                $actionType = 'add';
                $description = $_SESSION['username'] . " added a new client '$client_name' and pet '$pet_name'";
                logAction($pdo, $_SESSION['vet_id'], $actionType, $description, 'Admin');

                // Commit transaction
                $pdo->commit();

                header('Location: clients.php?message=Client and pet added successfully');
                exit;
            } elseif (isset($_POST['update_client'])) {
                $client_id = (int)$_POST['client_id'];
                $pet_id = (int)$_POST['pet_id'];

                // Update client
                $stmt = $pdo->prepare("UPDATE Client SET client_name=?, client_address=?, client_contact_number=? WHERE client_id=?");
                $stmt->execute([$client_name, $client_address, $client_contact, $client_id]);

                // Update pet
                $stmt = $pdo->prepare("UPDATE Pet SET pet_name=?, pet_sex=?, pet_weight=?, pet_breed=?, pet_birth_date=?, pet_species=? WHERE pet_id=? AND client_id=?");
                $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_id, $client_id]);

                // Log the update action
                $actionType = 'update';
                $description = $_SESSION['username'] . " updated client '$client_name' with pet '$pet_name'";
                logAction($pdo, $_SESSION['vet_id'], $actionType, $description, 'Admin');

                header('Location: clients.php?message=Client and pet updated successfully');
                exit;
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
        $stmt = $pdo->prepare("SELECT client_name FROM client WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        $client_name = $client['client_name'] ?? 'Unknown';

        // Begin transaction
        $pdo->beginTransaction();

        // ✅ Fix: Use client_id to archive all their pets
        $stmt = $pdo->prepare("UPDATE pet SET status = 0 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Archive the client
        $stmt = $pdo->prepare("UPDATE client SET status = 0 WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Log the action
        $actionType = 'delete';
        $description = $_SESSION['username'] . " archived client '$client_name'";
        logAction($pdo, $_SESSION['vet_id'], $actionType, $description, 'Admin');

        // Commit transaction
        $pdo->commit();

        header('Location: clients.php?message=Client and associated pets archived successfully');
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Database error: Cannot archive client and pets. " . $e->getMessage();
    }
}



/**
 * Fetch client data for editing
 */
function getDataToEdit($pdo)
{
    $clientToEdit = null;
    $petToEdit = null;
    $error = null;

    if (isset($_GET['edit_client_id']) && is_numeric($_GET['edit_client_id'])) {
        try {
            // Get client info
            $stmt = $pdo->prepare("SELECT * FROM Client WHERE client_id = ?");
            $stmt->execute([(int)$_GET['edit_client_id']]);
            $clientToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get the first pet for this client
            if ($clientToEdit) {
                $stmt = $pdo->prepare("SELECT * FROM Pet WHERE client_id = ? LIMIT 1");
                $stmt->execute([(int)$_GET['edit_client_id']]);
                $petToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }

    return [
        'client' => $clientToEdit,
        'pet' => $petToEdit,
        'error' => $error
    ];
}

// Get data for editing
$editData = getDataToEdit($pdo);
$clientToEdit = $editData['client'];
$petToEdit = $editData['pet'];
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
