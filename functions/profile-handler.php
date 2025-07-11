<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Check if user is logged in by verifying vet_id session
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Fetch veterinarian data for the logged-in user
 */
$stmt = $pdo->prepare("SELECT * FROM Veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$vet = $stmt->fetch();

/**
 * Handle updating veterinarian profile via POST request
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['vet_name'];
    $contact = $_POST['vet_contact_number'];
    $username = $_POST['vet_username'];

    if (!empty($_POST['vet_password'])) {
        $password = password_hash($_POST['vet_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ?, vet_password = ? WHERE vet_id = ?");
        $stmt->execute([$name, $contact, $username, $password, $_SESSION['vet_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ? WHERE vet_id = ?");
        $stmt->execute([$name, $contact, $username, $_SESSION['vet_id']]);
    }

    header('Location: profile.php?success=1');
    exit;
}
