<?php
// Enable error reporting for debugging (disable in production)
if (!defined('PRODUCTION')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Load environment variables if .env file exists
$dotenvPath = __DIR__ . '/../../../.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) == 2) {
            $name = trim($parts[0]);
            $value = trim($parts[1]);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Database configuration
$servername = 'localhost';
$username = 'fellipe';
$password = 'rb0i#w6@nHXgH8!WR';
$dbname = 'game_platform';

// Validate configuration - only check for placeholders, not empty values
if (strpos($servername, '%%DB_HOST%%') !== false) {
    die("Database host not configured - placeholder not replaced");
}
if (strpos($username, '%%DB_USER_PROD%%') !== false) {
    die("Database username not configured - placeholder not replaced");
}
if (strpos($password, '%%DB_PASSWORD_PROD%%') !== false) {
    die("Database password not configured - placeholder not replaced");
}
if (strpos($dbname, '%%DB_NAME_PROD%%') !== false) {
    die("Database name not configured - placeholder not replaced");
}

// Additional validation for truly empty values (after placeholder replacement)
if (trim($servername) === '') {
    die("Database host is empty");
}
if (trim($username) === '') {
    die("Database username is empty");
}
// Note: password can be empty in some configurations, so we don't check it
if (trim($dbname) === '') {
    die("Database name is empty");
}

// Create connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }
    
    // Set connection options for better error handling
    $conn->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}
?>
