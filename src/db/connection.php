<?php
// Load .env file variables
$dotenvPath = __DIR__ . '/../../.env'; // Assumes .env is in the project root
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) { // Skip comments
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

$servername = getenv('DB_SERVERNAME') ?: 'localhost'; // Load from environment or use default
$username = getenv('DB_USERNAME') ?: 'root'; // Load from environment or use default
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME') ?: 'game_platform'; // Load from environment or use default

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4 for full Unicode support
if (!$conn->set_charset("utf8mb4")) {
    throw new Exception("Error loading character set utf8mb4: " . $conn->error);
}

// The connection object $conn can now be used to perform database operations.
// For example, to close the connection when it's no longer needed:
// $conn->close();

// It's common to include this file in other PHP scripts that need database access.
// Avoid outputting HTML or any other content from this file if it's meant to be purely a connection script.
?>
