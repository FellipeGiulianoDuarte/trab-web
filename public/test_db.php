<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão Simples</h1>";

// Check if placeholders are still there
$host = '%%DB_HOST%%';
$user = '%%DB_USER_PROD%%';
$pass = '%%DB_PASSWORD_PROD%%';
$db = '%%DB_NAME_PROD%%';

if (strpos($host, '%%') !== false) {
    echo "❌ Placeholders não foram substituídos no deploy!<br>";
    echo "Host: " . $host . "<br>";
    echo "User: " . $user . "<br>";
    echo "DB: " . $db . "<br>";
    exit;
}

echo "Tentando conectar com:<br>";
echo "Host: " . $host . "<br>";
echo "User: " . $user . "<br>";
echo "Database: " . $db . "<br><br>";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Conexão estabelecida com sucesso!<br>";
    
    // Test basic query
    $result = $conn->query("SELECT VERSION() as version");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "MySQL Version: " . $row['version'] . "<br>";
    }
    
    // Test tables
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<br>Tabelas encontradas:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    }
    
    $conn->close();
    echo "<br>✅ Teste concluído com sucesso!";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
