<?php
require_once 'public/backend/db/connection.php';

echo "✅ Database connection successful!\n";
echo "Connected to: " . $conn->server_info . "\n";
echo "Database: " . $dbname . "\n";
echo "Host: " . $servername . "\n";
echo "User: " . $username . "\n";

// Test a simple query
$result = $conn->query("SELECT DATABASE() as current_db");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Current database: " . $row['current_db'] . "\n";
} else {
    echo "Could not get current database info\n";
}

$conn->close();
?>
