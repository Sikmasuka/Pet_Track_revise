<?php
// Make sure there are no spaces or characters before this opening PHP tag
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/auth.php";

// Set content type to JSON
header('Content-Type: application/json');

// Disable error display to prevent HTML in JSON response
ini_set('display_errors', 0);

try {
    // Check if POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get form data
    $user_type = trim($_POST['user_type'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate required fields
    if (empty($name) || empty($username)) {
        throw new Exception('Name and username are required');
    }
    if ($user_type === 'vet' && empty($contact_number)) {
        throw new Exception('Contact number is required for veterinarians');
    }

    if ($user_type === 'admin') {
        // Check if user is logged in as admin
        if (!isset($_SESSION['admin_id'])) {
            throw new Exception('Admin not authenticated');
        }
        $user_id = $_SESSION['admin_id'];

        // Check if username is already taken by another admin
        $stmt = $pdo->prepare("SELECT admin_id FROM admin WHERE admin_username = ? AND admin_id != ?");
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetch()) {
            throw new Exception('Username is already taken');
        }

        // Prepare update query for admin
        if (!empty($password)) {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin SET admin_name = ?, admin_username = ?, admin_password = ? WHERE admin_id = ?");
            $result = $stmt->execute([$name, $username, $hashed_password, $user_id]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE admin SET admin_name = ?, admin_username = ? WHERE admin_id = ?");
            $result = $stmt->execute([$name, $username, $user_id]);
        }

        // Update session
        if ($result) {
            $_SESSION['admin_name'] = $name;
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            throw new Exception('Failed to update admin profile');
        }
    } elseif ($user_type === 'vet') {
        // Check if user is logged in as vet
        if (!isset($_SESSION['vet_id'])) {
            throw new Exception('User not authenticated');
        }
        $user_id = $_SESSION['vet_id'];

        // Check if username is already taken by another vet
        $stmt = $pdo->prepare("SELECT vet_id FROM veterinarian WHERE vet_username = ? AND vet_id != ?");
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetch()) {
            throw new Exception('Username is already taken');
        }

        // Prepare update query for vet
        if (!empty($password)) {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ?, vet_password = ? WHERE vet_id = ?");
            $result = $stmt->execute([$name, $contact_number, $username, $hashed_password, $user_id]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ? WHERE vet_id = ?");
            $result = $stmt->execute([$name, $contact_number, $username, $user_id]);
        }

        // Update session
        if ($result) {
            $_SESSION['vet_name'] = $name;
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            throw new Exception('Failed to update vet profile');
        }
    } else {
        throw new Exception('Invalid user type');
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
