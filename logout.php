<?php
// Start the session to access user data
session_start();

// Debug output to confirm execution
ob_start(); // Start output buffering to prevent headers already sent
echo "Logout script running in: " . __DIR__ . "<br>";
echo "Session data: " . (isset($_SESSION['admin_id']) ? 'Admin' : (isset($_SESSION['vet_id']) ? 'Vet' : 'None')) . "<br>";

// Include database connection and logging function
try {
    require_once 'db.php';
    require_once './functions/logs.php';
    echo "Files included successfully.<br>";
} catch (Exception $e) {
    error_log('Error including files in logout.php: ' . $e->getMessage());
    echo "Error including files: " . $e->getMessage() . "<br>";
}


// Destroy all session data to log out the user
session_unset();
session_destroy();
echo "Session destroyed.<br>";

// Redirect to the login page with full URL
$redirectUrl = "http://localhost/Pet_Track_revise-3/index.php";
echo "Redirecting to: " . $redirectUrl . "<br>";
header('Location: ' . $redirectUrl);
ob_end_flush(); // Flush output buffer
exit;
