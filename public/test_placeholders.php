<?php
// Test specifically for placeholder replacement
echo "<h1>Teste de Placeholders</h1>";

// Check raw content of connection.php
$connection_file = __DIR__ . '/backend/db/connection.php';
if (file_exists($connection_file)) {
    echo "<h2>Conteúdo do connection.php:</h2>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($connection_file));
    echo "</pre>";
    
    // Check for unreplaced placeholders
    $content = file_get_contents($connection_file);
    if (strpos($content, '%%DB_HOST%%') !== false) {
        echo "<p style='color: red;'>❌ DB_HOST placeholder não foi substituído</p>";
    } else {
        echo "<p style='color: green;'>✅ DB_HOST placeholder foi substituído</p>";
    }
    
    if (strpos($content, '%%DB_USER_PROD%%') !== false) {
        echo "<p style='color: red;'>❌ DB_USER_PROD placeholder não foi substituído</p>";
    } else {
        echo "<p style='color: green;'>✅ DB_USER_PROD placeholder foi substituído</p>";
    }
    
    if (strpos($content, '%%DB_PASSWORD_PROD%%') !== false) {
        echo "<p style='color: red;'>❌ DB_PASSWORD_PROD placeholder não foi substituído</p>";
    } else {
        echo "<p style='color: green;'>✅ DB_PASSWORD_PROD placeholder foi substituído</p>";
    }
    
    if (strpos($content, '%%DB_NAME_PROD%%') !== false) {
        echo "<p style='color: red;'>❌ DB_NAME_PROD placeholder não foi substituído</p>";
    } else {
        echo "<p style='color: green;'>✅ DB_NAME_PROD placeholder foi substituído</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo connection.php não encontrado</p>";
}

echo "<h2>Variáveis de Ambiente:</h2>";
echo "<pre>";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'DB_') === 0) {
        echo $key . " = " . (empty($value) ? '[EMPTY]' : '[SET]') . "\n";
    }
}
echo "</pre>";
?>
