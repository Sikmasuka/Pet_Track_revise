<?php
// Start the session to manage user login state
session_start();

// Destroy all session data to log out the user
session_unset();
session_destroy();

// Redirect to the login page after logout
header('Location: index.php');
exit;
