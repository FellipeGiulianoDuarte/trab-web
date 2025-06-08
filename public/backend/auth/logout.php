<?php
require_once __DIR__ . '/session_manager.php';

SessionManager::destroySession();

setcookie('success_message', "You have been successfully logged out.", time() + 10, "/");

header("Location: ../../login.php");

exit;
?>
