<?php
require_once __DIR__ . '/session_manager.php';

if (!SessionManager::isAuthenticated()) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error_message'] = "You must be logged in to view this page.";

    header("Location: login.php");
    exit;
}

SessionManager::refreshCookie();
?>
