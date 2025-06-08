<?php
$dotenvPath = __DIR__ . '/../../../.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
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

$servername = '%%DB_HOST%%';
$username = '%%DB_USER_PROD%%';
$password = '%%DB_PASSWORD_PROD%%';
$dbname = '%%DB_NAME_PROD%%';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

if (!$conn->set_charset("utf8mb4")) {
    throw new Exception("Error loading character set utf8mb4: " . $conn->error);
}
?>
