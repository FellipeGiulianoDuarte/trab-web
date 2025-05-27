<?php
// Step 1: Start the existing session to access it
session_start();

// Step 2: Unset all session variables
session_unset(); // or $_SESSION = array();

// Step 3: Destroy the session
session_regenerate_id(true);
session_destroy();

// Step 4: Use a flash mechanism to store the logout message
// This avoids starting a new session immediately after destroying the old one.
setcookie('success_message', "You have been successfully logged out.", time() + 10, "/");

// Step 5: Redirect the user to the login page
header("Location: ../../public/login.php");

// Step 7: Ensure no further script execution after redirection
exit;
?>
