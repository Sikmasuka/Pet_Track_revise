<?php
// Include the database connection
require_once __DIR__ . '/../db.php';
require_once 'logs.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/admin-dashboard.php');
    exit;
} elseif (isset($_SESSION['vet_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = ''; // Variable to store login error or success message
$login_success = false; // Flag to trigger SweetAlert2
$redirect_url = ''; // Store the redirect URL

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
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
    if (!$user) {
        // Username does not exist in either table
        $message = 'Invalid username';
    } else {
        // Username exists, verify password
        if ($user['role'] === 'admin') {
            // Admin password is not hashed
            if ($password === $user['password']) {
                // Get the admin_id using the username
                $stmt2 = $pdo->prepare("SELECT admin_id FROM Admin WHERE admin_username = :username");
                $stmt2->execute(['username' => $username]);
                $admin = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($admin) {
                    // Password matches, set session
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = 'admin';
                    $login_success = true;

                    $actionType = 'Login';
                    $description = $_SESSION['username'] . ' Successfully Logged in';
                    logAction($pdo, $admin['admin_id'], $actionType, $description, 'Admin');
                    $redirect_url = './admin/admin-dashboard.php';
                }
            } else {
                // Incorrect password for admin
                $message = 'Incorrect password';
            }
        } else {
            // Veterinarian password is hashed
            if (password_verify($password, $user['password'])) {
                // Get vet_id and vet_name
                $stmt2 = $pdo->prepare("SELECT vet_id, vet_name FROM Veterinarian WHERE vet_username = :username");
                $stmt2->execute(['username' => $username]);
                $vet = $stmt2->fetch(PDO::FETCH_ASSOC);

                // Set session
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'veterinarian';
                $_SESSION['vet_id'] = $vet['vet_id'];
                $_SESSION['vet_name'] = $vet['vet_name'];

                $login_success = true;
                $actionType = 'Login';
                $description = $vet['vet_name'] . ' Successfully Logged in';
                logAction($pdo, $vet['vet_id'], $actionType, $description, 'Veterinarian');
                $redirect_url = 'dashboard.php';
            } else {
                // Incorrect password for veterinarian
                $message = 'Incorrect password';
            }
        }
    }
}
