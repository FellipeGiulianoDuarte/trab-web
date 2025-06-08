<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não está logado']);
    exit();
}

// Get the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['score']) || !is_numeric($data['score'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Pontuação inválida']);
    exit();
}

$score = (int)$data['score'];
$userId = $_SESSION['user_id'];

// Validate score range (reasonable limits)
if ($score < 0 || $score > 1200) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Pontuação fora do limite permitido']);
    exit();
}

try {
    // Include database connection
    require_once '../db/connection.php';
    
    // Prepare and execute the insert query using mysqli
    $stmt = $conn->prepare("INSERT INTO games (user_id, score, played_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $score);
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Pontuação salva com sucesso',
            'score' => $score
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar pontuação: ' . $conn->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Database error in save_score.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor']);
}
?>
