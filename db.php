<?php
// Global database connection configuration
$host = 'localhost'; // Change if necessary
$dbname = 'pettrackdb'; // Your database name
$username = 'root'; // Database username (change if necessary)
$password = ''; // Database password (change if necessary)

try {
    // Create a PDO instance for database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Could not connect to the database: " . $e->getMessage());
}
