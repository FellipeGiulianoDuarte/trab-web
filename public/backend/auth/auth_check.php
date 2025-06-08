<?php
require_once __DIR__ . '/session_manager.php';

// Check if user is authenticated (session or cookie)
if (!SessionManager::isAuthenticated()) {
    // Store an error message to display on login page
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error_message'] = "You must be logged in to view this page.";

    // Redirect to login page.
    header("Location: login.php");
    exit;
}

// Optional: Refresh cookie on user activity to extend session
SessionManager::refreshCookie();

// If user is authenticated, the script does nothing further, and the protected page content will be executed.
?>
