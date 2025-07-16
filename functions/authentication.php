<?php
// Include the database connection
require_once 'db.php';

// Start the session to manage user login state
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['vet_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = ''; // Variable to store login error or success message
$login_success = false; // Flag to trigger SweetAlert2
$redirect_url = ''; // Store the redirect URL

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to check if the user exists (for both admin and veterinarian)
    $stmt = $pdo->prepare(
        "SELECT 'admin' AS role, admin_username AS username, admin_password AS password FROM Admin WHERE admin_username = :username
         UNION
         SELECT 'veterinarian' AS role, vet_username AS username, vet_password AS password FROM Veterinarian WHERE vet_username = :username"
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if ($user) {
        // If it's an admin, handle password differently (admin password is not hashed)
        if ($user['role'] === 'admin') {
            // Check if the password matches directly
            if ($password === $user['password']) {
                // Password matches, set session
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'admin';
                $login_success = true;
                $redirect_url = './admin/admin.php';
            }
        } else {
            // Veterinarian login handling (with hashed password)
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'veterinarian';
                // Get vet_id
                $stmt2 = $pdo->prepare("SELECT vet_id FROM Veterinarian WHERE vet_username = :username");
                $stmt2->execute(['username' => $username]);
                $vet = $stmt2->fetch(PDO::FETCH_ASSOC);
                $_SESSION['vet_id'] = $vet['vet_id'];
                $login_success = true;
                $redirect_url = 'dashboard.php';
            }
        }
        // If password doesn't match
        if (!$login_success) {
            $message = 'Invalid username or password.';
        }
    } else {
        // If no user found
        $message = 'Invalid username or password.';
    }
}
