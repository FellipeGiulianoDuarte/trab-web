<?php
// Quick test to verify if placeholders were replaced
echo "<h2>Teste de Placeholders - Deploy</h2>";

// Read the connection file directly and check for placeholders
$connectionFile = __DIR__ . '/backend/db/connection.php';

if (!file_exists($connectionFile)) {
    echo "❌ Arquivo connection.php não encontrado!<br>";
    exit;
}

$content = file_get_contents($connectionFile);

echo "<h3>Verificando placeholders:</h3>";

$placeholders = [
    '%%DB_HOST%%' => 'DB_HOST',
    '%%DB_USER_PROD%%' => 'DB_USER_PROD', 
    '%%DB_PASSWORD_PROD%%' => 'DB_PASSWORD_PROD',
    '%%DB_NAME_PROD%%' => 'DB_NAME_PROD'
];

$allReplaced = true;

foreach ($placeholders as $placeholder => $name) {
    if (strpos($content, $placeholder) !== false) {
        echo "❌ $name ainda contém placeholder: $placeholder<br>";
        $allReplaced = false;
    } else {
        echo "✅ $name foi substituído corretamente<br>";
    }
}

if ($allReplaced) {
    echo "<h3 style='color: green;'>✅ Todos os placeholders foram substituídos!</h3>";
    
    // Now test the actual connection
    echo "<h3>Testando conexão:</h3>";
    try {
        require_once $connectionFile;
        echo "✅ Arquivo de conexão carregado sem erros<br>";
        
        if (isset($conn)) {
            echo "✅ Variável \$conn definida<br>";
            echo "✅ Conexão estabelecida com sucesso!<br>";
        } else {
            echo "❌ Variável \$conn não foi definida<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<h3 style='color: red;'>❌ Deploy não substituiu os placeholders corretamente!</h3>";
    echo "<h4>Conteúdo do arquivo (primeiras 15 linhas):</h4>";
    echo "<pre>";
    $lines = explode("\n", $content);
    for ($i = 0; $i < min(15, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
}
?>
