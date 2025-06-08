<?php
// Simple diagnostic script to check for common issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico do Sistema</h1>";

// Test 1: Database connection
echo "<h2>1. Teste de Conexão do Banco</h2>";
try {
    require_once __DIR__ . '/backend/db/connection.php';
    echo "✅ Conexão com banco estabelecida<br>";
    
    // Test database and tables
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "✅ Tabelas no banco:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Test 2: Session check
echo "<h2>2. Teste de Sessão</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ Usuário logado - ID: " . $_SESSION['user_id'] . "<br>";
} else {
    echo "⚠️ Usuário não está logado<br>";
    // Create a test session for diagnostic
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'test_user';
    echo "🔧 Criada sessão de teste<br>";
}

// Test 3: Database queries
echo "<h2>3. Teste de Consultas</h2>";
if (isset($conn)) {
    try {
        // Test users table
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        echo "✅ Usuários na base: " . $count['count'] . "<br>";
        
        // Test games table
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM games");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        echo "✅ Jogos na base: " . $count['count'] . "<br>";
        
        // Test leagues table
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM leagues");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        echo "✅ Ligas na base: " . $count['count'] . "<br>";
        
        // Test league_members table
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM league_members");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        echo "✅ Membros de ligas na base: " . $count['count'] . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Erro nas consultas: " . $e->getMessage() . "<br>";
    }
}

// Test 4: Critical files
echo "<h2>4. Teste de Arquivos</h2>";
$critical_files = [
    'backend/db/connection.php',
    'backend/auth/auth_check.php',
    'scores.php',
    'leagues.php'
];

foreach ($critical_files as $file) {
    if (file_exists($file)) {
        echo "✅ " . $file . " existe<br>";
        if (is_readable($file)) {
            echo "  ✅ Arquivo legível<br>";
        } else {
            echo "  ❌ Arquivo não legível<br>";
        }
    } else {
        echo "❌ " . $file . " não encontrado<br>";
    }
}

// Test 5: PHP version and extensions
echo "<h2>5. Informações do PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQLi Extension: " . (extension_loaded('mysqli') ? '✅ Ativo' : '❌ Inativo') . "<br>";
echo "Session Extension: " . (extension_loaded('session') ? '✅ Ativo' : '❌ Inativo') . "<br>";

echo "<h2>6. Teste das Páginas Problemáticas</h2>";

// Test scores page logic
echo "<h3>Testando lógica do scores.php:</h3>";
if (isset($conn) && isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM games WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userGames = $result->fetch_assoc();
        echo "✅ Consulta de jogos do usuário OK - " . $userGames['count'] . " jogos<br>";
    } catch (Exception $e) {
        echo "❌ Erro na consulta de jogos: " . $e->getMessage() . "<br>";
    }
}

// Test leagues page logic
echo "<h3>Testando lógica do leagues.php:</h3>";
if (isset($conn) && isset($_SESSION['user_id'])) {
    try {
        $leagues_query = "SELECT l.id, l.name, l.created_at, u.username as creator_name,
                          COUNT(DISTINCT lm.user_id) as member_count,
                          SUM(CASE WHEN lm.user_id = ? THEN 1 ELSE 0 END) as is_member
                          FROM leagues l
                          JOIN users u ON l.creator_user_id = u.id
                          LEFT JOIN league_members lm ON l.id = lm.league_id
                          GROUP BY l.id, l.name, l.created_at, u.username
                          ORDER BY l.created_at DESC
                          LIMIT 5";
        $stmt = $conn->prepare($leagues_query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $leagues = $result->fetch_all(MYSQLI_ASSOC);
        echo "✅ Consulta de ligas OK - " . count($leagues) . " ligas encontradas<br>";
    } catch (Exception $e) {
        echo "❌ Erro na consulta de ligas: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>Diagnóstico Completo!</h2>";
echo "Verifique os resultados acima para identificar problemas.";
?>
