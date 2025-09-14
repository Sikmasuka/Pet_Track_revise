<?php
// functions/authentication.php

require_once __DIR__ . '/../db.php';
require_once 'logs.php';

// Initialize variables
$message = '';
$login_success = false;
$redirect_url = '';

// If already logged in, redirect
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/admin-dashboard.php');
    exit;
} elseif (isset($_SESSION['vet_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    // ✅ Step 1: Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Invalid request. Please refresh and try again.';
        return;
    }

    // ✅ Step 2: Get inputs safely
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $message = 'Username and password are required.';
        return;
    }

    // ✅ Step 5: Check both Admin and Veterinarian tables
    $stmt = $pdo->prepare("
        SELECT 'admin' AS role, admin_id AS id, admin_username AS username, admin_password AS password 
        FROM Admin WHERE admin_username = :username
        UNION
        SELECT 'veterinarian' AS role, vet_id AS id, vet_username AS username, vet_password AS password 
        FROM Veterinarian WHERE vet_username = :username
    ");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = 'Invalid username';
        return;
    }

    // ✅ Check password with password_verify() for both roles
    if (password_verify($password, $user['password'])) {

        // Regenerate session ID for security
        session_regenerate_id(true);

        if ($user['role'] === 'admin') {
            // Admin login
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';

            $login_success = true;
            $redirect_url = './admin/admin-dashboard.php';

            logAction($pdo, $user['id'], 'Login', $_SESSION['username'] . ' logged in', 'Admin');
        } else {
            // Veterinarian login
            // Fetch vet_name for session
            $stmt2 = $pdo->prepare("SELECT vet_name FROM Veterinarian WHERE vet_id = :id");
            $stmt2->execute(['id' => $user['id']]);
            $vet = $stmt2->fetch(PDO::FETCH_ASSOC);

            $_SESSION['vet_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['vet_name'] = $vet['vet_name'] ?? '';
            $_SESSION['role'] = 'veterinarian';

            $login_success = true;
            $redirect_url = 'dashboard.php';

            logAction($pdo, $user['id'], 'Login', $_SESSION['vet_name'] . ' logged in', 'Veterinarian');
        }
    } else {
        // Invalid password
        $message = 'Incorrect password';
    }
}
