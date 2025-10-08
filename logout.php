<?php
session_start();
ob_start();

try {
    require_once 'db.php';
    require_once './functions/logs.php';
} catch (Exception $e) {
    error_log('Error including files in logout.php: ' . $e->getMessage());
}

// Destroy all session data
session_unset();
session_destroy();

// ✅ Securely delete the "remember me" cookie
setcookie('remember_username', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

ob_end_clean();
header("Location: /Pet-Track/index.php");
exit;
