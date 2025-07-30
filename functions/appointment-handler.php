<?php
session_start();
require_once __DIR__ . "/../db.php"; // Adjust path to your PDO connection file
try {
    // Get form data
    $owner_name = $_POST['owner_name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $reason = $_POST['reason'] ?? '';

    // Validate inputs
    if (empty($owner_name) || empty($contact_number) || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../landing-page.php");
        exit();
    }

    // Prepare and execute the insert query
    $stmt = $pdo->prepare("
        INSERT INTO appointments (owner_name, contact_number, appointment_date, appointment_time, reason, status, created_at, updated_at)
        VALUES (:owner_name, :contact_number, :appointment_date, :appointment_time, :reason, 0, NOW(), NOW())
    ");
    $stmt->execute([
        'owner_name' => $owner_name,
        'contact_number' => $contact_number,
        'appointment_date' => $appointment_date,
        'appointment_time' => $appointment_time,
        'reason' => $reason
    ]);

    $_SESSION['success'] = "Appointment booked successfully!";
    header("Location: ../landing-page.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error booking appointment: " . $e->getMessage();
    header("Location: ../landing-page.php");
    exit();
}
