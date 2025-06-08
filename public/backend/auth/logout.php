<?php
require_once __DIR__ . '/session_manager.php';

// Destroy session and remove cookies
SessionManager::destroySession();

// Use a flash mechanism to store the logout message
setcookie('success_message', "You have been successfully logged out.", time() + 10, "/");

// Redirect the user to the login page
header("Location: ../../login.php");

// Ensure no further script execution after redirection
exit;
?>
