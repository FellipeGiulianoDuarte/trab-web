<?php
// Step 1: Start the existing session to access it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Step 2: Unset all session variables
session_unset(); // or $_SESSION = array();

// Step 3: Destroy the session
session_destroy();

// Step 4: Use a flash mechanism to store the logout message
// This avoids starting a new session immediately after destroying the old one.
// The path "/" for the cookie is correct as it means the cookie is available for the entire domain.
setcookie('success_message', "You have been successfully logged out.", time() + 10, "/");

// Step 5: Redirect the user to the login page
header("Location: /login.php"); // Corrected path

// Step 7: Ensure no further script execution after redirection
exit;
?>
