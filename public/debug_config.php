<?php
echo "<h1>Debug Detalhado de Configuração</h1>";

// Testar os valores antes da validação
$servername = '%%DB_HOST%%';
$username = '%%DB_USER_PROD%%';
$password = '%%DB_PASSWORD_PROD%%';
$dbname = '%%DB_NAME_PROD%%';

echo "<h2>Valores Brutos:</h2>";
echo "servername: '" . $servername . "' (length: " . strlen($servername) . ")<br>";
echo "username: '" . $username . "' (length: " . strlen($username) . ")<br>";
echo "password: '" . $password . "' (length: " . strlen($password) . ")<br>";
echo "dbname: '" . $dbname . "' (length: " . strlen($dbname) . ")<br>";

echo "<h2>Testes de Validação:</h2>";

// Teste 1: Verificar se ainda tem placeholders
echo "Tem placeholder DB_HOST? " . (strpos($servername, '%%DB_HOST%%') !== false ? "SIM" : "NÃO") . "<br>";
echo "Tem placeholder DB_USER_PROD? " . (strpos($username, '%%DB_USER_PROD%%') !== false ? "SIM" : "NÃO") . "<br>";
echo "Tem placeholder DB_PASSWORD_PROD? " . (strpos($password, '%%DB_PASSWORD_PROD%%') !== false ? "SIM" : "NÃO") . "<br>";
echo "Tem placeholder DB_NAME_PROD? " . (strpos($dbname, '%%DB_NAME_PROD%%') !== false ? "SIM" : "NÃO") . "<br>";

// Teste 2: Verificar se está vazio
echo "<br>servername empty? " . (empty($servername) ? "SIM" : "NÃO") . "<br>";
echo "username empty? " . (empty($username) ? "SIM" : "NÃO") . "<br>";
echo "password empty? " . (empty($password) ? "SIM" : "NÃO") . "<br>";
echo "dbname empty? " . (empty($dbname) ? "SIM" : "NÃO") . "<br>";

// Teste 3: Verificar condição combinada
echo "<br>Condição servername: " . ((empty($servername) || $servername == '%%DB_HOST%%') ? "FALHA" : "PASSA") . "<br>";
echo "Condição username: " . ((empty($username) || $username == '%%DB_USER_PROD%%') ? "FALHA" : "PASSA") . "<br>";
echo "Condição password: " . ((empty($password) || $password == '%%DB_PASSWORD_PROD%%') ? "FALHA" : "PASSA") . "<br>";
echo "Condição dbname: " . ((empty($dbname) || $dbname == '%%DB_NAME_PROD%%') ? "FALHA" : "PASSA") . "<br>";

echo "<h2>Arquivo connection.php atual:</h2>";
echo "<pre>";
echo htmlspecialchars(file_get_contents(__DIR__ . '/backend/db/connection.php'));
echo "</pre>";
?>
