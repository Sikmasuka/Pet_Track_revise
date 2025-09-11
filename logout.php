<?php
session_start();
ob_start();

try {
    require_once 'db.php';
    require_once './functions/logs.php';
} catch (Exception $e) {
    error_log('Error including files in logout.php: ' . $e->getMessage());
}

session_unset();
session_destroy();
ob_end_clean();
header("Location: /Pet_Track_revise-3/index.php");
exit;
