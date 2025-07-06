<?php
// Include the database connection
require_once 'db.php';

// Start the session to manage user login state
session_start();

$message = ''; // Variable to store login error or success message

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
            // Check if the password matches directly (since admin password isn't hashed)
            if ($password === $user['password']) {
                // Password matches, set session and redirect to admin page
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'admin';
                header('Location: admin.php');
                exit;
            }
        } else {
            // Veterinarian login handling (with hashed password)
            if (password_verify($password, $user['password'])) {
                // Password matches, set session and redirect to dashboard
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'veterinarian';
                // Query to get the vet_id from the database
                $stmt2 = $pdo->prepare("SELECT vet_id FROM Veterinarian WHERE vet_username = :username");
                $stmt2->execute(['username' => $username]);
                $vet = $stmt2->fetch(PDO::FETCH_ASSOC);
                $_SESSION['vet_id'] = $vet['vet_id'];
                header('Location: dashboard.php');
                exit;
            }
        }
        // If password doesn't match
        $message = 'Invalid username or password.';
    } else {
        // If no user found
        $message = 'Invalid username or password.';
    }
}
