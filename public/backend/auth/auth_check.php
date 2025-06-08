<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user_id is not set in session, or is empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Store an error message to display on login page
    $_SESSION['error_message'] = "You must be logged in to view this page.";

    // Redirect to login page.
    // This path assumes auth_check.php is in a subdirectory (e.g., src/auth)
    // and the page including it is directly in the public/ directory (e.g. public/index.php).
    // So, from public/index.php, login.php is in the same directory.
    header("Location: login.php");
    exit;
}

// If user_id is set, the script does nothing further, and the protected page content will be executed.
?>
