<?php
// Enable error reporting for debugging
if (!defined('PRODUCTION')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include session manager
$session_manager_path = __DIR__ . '/session_manager.php';
if (!file_exists($session_manager_path)) {
    die("Session manager not found at: " . $session_manager_path);
}
require_once $session_manager_path;

// Check if user is authenticated
try {
    if (!SessionManager::isAuthenticated()) {
        $_SESSION['error_message'] = "You must be logged in to view this page.";
        
        // Determine correct redirect path
        $current_dir = dirname($_SERVER['REQUEST_URI']);
        $login_path = '/login.php';
        
        header("Location: " . $login_path);
        exit;
    }
    
    // Refresh session cookie
    SessionManager::refreshCookie();
} catch (Exception $e) {
    // Log error and redirect to login
    error_log("Auth check error: " . $e->getMessage());
    header("Location: /login.php");
    exit;
}
?>
