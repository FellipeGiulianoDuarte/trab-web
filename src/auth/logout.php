<?php
// Step 1: Start the existing session to access it
session_start();

// Step 2: Unset all session variables
session_unset(); // or $_SESSION = array();

// Step 3: Destroy the session
session_destroy();

// Step 4: Start a new session specifically for the logout message
// This is important because session_destroy() removes the session file,
// so a new one is needed to carry the message to the next page.
session_start();

// Step 5: Set the success message in the new session
$_SESSION['success_message'] = "You have been successfully logged out.";

// Step 6: Redirect the user to the login page
header("Location: ../../public/login.php");

// Step 7: Ensure no further script execution after redirection
exit;
?>
