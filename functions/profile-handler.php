<?php
// Make sure there are no spaces or characters before this opening PHP tag
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/auth.php";
// Your DB connection file

// Set content type to JSON
header('Content-Type: application/json');

// Disable error display to prevent HTML in JSON response
ini_set('display_errors', 0);

try {
    // Check if user is logged in
    if (!isset($_SESSION['vet_id'])) {
        throw new Exception('User not authenticated');
    }

    // Check if POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $vet_id = $_SESSION['vet_id'];

    // Get form data
    $vet_name = trim($_POST['vet_name'] ?? '');
    $vet_contact_number = trim($_POST['vet_contact_number'] ?? '');
    $vet_username = trim($_POST['vet_username'] ?? '');
    $vet_password = trim($_POST['vet_password'] ?? '');

    // Validate required fields
    if (empty($vet_name) || empty($vet_contact_number) || empty($vet_username)) {
        throw new Exception('All fields except password are required');
    }

    // Check if username is already taken by another vet
    $stmt = $pdo->prepare("SELECT vet_id FROM veterinarian WHERE vet_username = ? AND vet_id != ?");
    $stmt->execute([$vet_username, $vet_id]);
    if ($stmt->fetch()) {
        throw new Exception('Username is already taken');
    }

    // Prepare update query
    if (!empty($vet_password)) {
        // Update with password
        $hashed_password = password_hash($vet_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ?, vet_password = ? WHERE vet_id = ?");
        $result = $stmt->execute([$vet_name, $vet_contact_number, $vet_username, $hashed_password, $vet_id]);
    } else {
        // Update without password
        $stmt = $pdo->prepare("UPDATE veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ? WHERE vet_id = ?");
        $result = $stmt->execute([$vet_name, $vet_contact_number, $vet_username, $vet_id]);
    }

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    } else {
        throw new Exception('Failed to update profile');
    }
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    // Database error
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

// Make sure nothing else is output after this
exit;
