<?php
// Ensure session starts
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../db.php"; // Include your database connection
date_default_timezone_set('America/Los_Angeles'); // Set to PST

// Debug: Log script execution
file_put_contents('debug.log', "Script executed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Test database connection
try {
    $pdo->query("SELECT 1");
    file_put_contents('debug.log', "Database connection successful.\n", FILE_APPEND);
} catch (PDOException $e) {
    file_put_contents('debug.log', "Connection error: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: ../landing-page.php");
    exit();
}

// Get and log form data
$owner_name = trim($_POST['owner_name'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$appointment_date = trim($_POST['appointment_date'] ?? '');
$appointment_time = trim($_POST['appointment_time'] ?? '') . ':00';
$reason = trim($_POST['reason'] ?? '');

file_put_contents('debug.log', "Form data: " . print_r(['owner_name' => $owner_name, 'contact_number' => $contact_number, 'appointment_date' => $appointment_date, 'appointment_time' => $appointment_time, 'reason' => $reason], true) . "\n", FILE_APPEND);

try {
    // Basic validation
    if (empty($owner_name) || empty($contact_number) || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: ../landing-page.php");
        exit();
    }

    // Validate and sanitize date
    $dateObj = DateTime::createFromFormat('Y-m-d', $appointment_date);
    if ($dateObj === false || $appointment_date !== $dateObj->format('Y-m-d')) {
        $_SESSION['error'] = "Invalid appointment date. Use YYYY-MM-DD format.";
        header("Location: ../landing-page.php");
        exit();
    }

    // Ensure date is not in the past
    $today = new DateTime('now', new DateTimeZone('America/Los_Angeles')); // Current PST time
    if (new DateTime($appointment_date) < $today) {
        $_SESSION['error'] = "Cannot book appointments for past dates.";
        header("Location: ../landing-page.php");
        exit();
    }

    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO appointments (owner_name, contact_number, appointment_date, appointment_time, reason, status)
        VALUES (:owner_name, :contact_number, :appointment_date, :appointment_time, :reason, 'Scheduled')
    ");
    $params = [
        'owner_name' => $owner_name,
        'contact_number' => $contact_number,
        'appointment_date' => $appointment_date,
        'appointment_time' => $appointment_time,
        'reason' => $reason
    ];
    $stmt->execute($params);

    // Verify insertion
    $lastId = $pdo->lastInsertId();
    if ($lastId > 0) {
        $_SESSION['success'] = "Appointment booked successfully! (ID: $lastId)";
    } else {
        $_SESSION['error'] = "Appointment was not saved. Please try again.";
    }
    header("Location: ../landing-page.php");
    exit();
} catch (PDOException $e) {
    file_put_contents('debug.log', "Insert error: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['error'] = "Failed to book appointment: " . $e->getMessage();
    header("Location: ../landing-page.php");
    exit();
} catch (Exception $e) {
    file_put_contents('debug.log', "General error: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['error'] = "An unexpected error occurred: " . $e->getMessage();
    header("Location: ../landing-page.php");
    exit();
}
