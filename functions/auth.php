<?php
// auth.php
// Define base URL for redirects
define('BASE_URL', '/Pet_Track_revise-3/');

// Protects admin pages
function requireAdmin()
{
    if (!isset($_SESSION['admin_id'])) {
        // Blocked if not admin
        header("Location: " . BASE_URL . "index.php");
        exit;
    }

    // Prevent vet from accessing admin page
    if (isset($_SESSION['vet_id'])) {
        header("Location: " . BASE_URL . "dashboard.php");
        exit;
    }
}

// Protects vet pages
function requireVet()
{
    if (!isset($_SESSION['vet_id'])) {
        // Blocked if not vet
        header("Location: " . BASE_URL . "index.php");
        exit;
    }

    // Prevent admin from accessing vet page
    if (isset($_SESSION['admin_id'])) {
        header("Location: " . BASE_URL . "admin/admin-dashboard.php");
        exit;
    }
}
