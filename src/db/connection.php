<?php
$servername = getenv('DB_SERVERNAME') ?: 'localhost'; // Load from environment or use default
$username = getenv('DB_USERNAME') ?: 'root'; // Load from environment or use default
$password = getenv('DB_PASSWORD') ?: ''; // Load from environment or use default
$dbname = getenv('DB_NAME') ?: 'game_platform'; // Load from environment or use default

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4 for full Unicode support
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s
", $conn->error);
    // Consider whether to die() here or proceed with default charset
}

// The connection object $conn can now be used to perform database operations.
// For example, to close the connection when it's no longer needed:
// $conn->close();

// It's common to include this file in other PHP scripts that need database access.
// Avoid outputting HTML or any other content from this file if it's meant to be purely a connection script.
?>
