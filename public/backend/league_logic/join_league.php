<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../leagues.php');
    exit;
}

// Get form data
$league_id = intval($_POST['league_id'] ?? 0);
$keyword = trim($_POST['keyword'] ?? '');
$user_id = $_SESSION['user_id'];

// Validate input
if ($league_id <= 0 || empty($keyword)) {
    handleErrorAndRedirect('ID da liga e palavra-chave são obrigatórios.', '../../leagues.php');
}

// Check if league exists and verify keyword
$check_league_query = "SELECT id, name, keyword FROM leagues WHERE id = ?";
$stmt = $conn->prepare($check_league_query);
$stmt->bind_param("i", $league_id);
$stmt->execute();
$league_result = $stmt->get_result();

if ($league_result->num_rows === 0) {
    $_SESSION['league_error'] = 'Liga não encontrada.';
    header('Location: ../../leagues.php');
    exit;
}

$league = $league_result->fetch_assoc();

// Verify keyword
if (htmlspecialchars_decode($league['keyword']) !== $keyword) {
    $_SESSION['league_error'] = 'Palavra-chave incorreta para a liga "' . htmlspecialchars($league['name']) . '".';
    header('Location: ../../leagues.php');
    exit;
}

// Check if user is already a member
$check_member_query = "SELECT id FROM league_members WHERE league_id = ? AND user_id = ?";
$stmt2 = $conn->prepare($check_member_query);
$stmt2->bind_param("ii", $league_id, $user_id);
$stmt2->execute();
$member_result = $stmt2->get_result();

if ($member_result->num_rows > 0) {
    $_SESSION['league_error'] = 'Você já é membro da liga "' . htmlspecialchars($league['name']) . '".';
    header('Location: ../../leagues.php');
    exit;
}

try {
    // Add user to league
    $insert_member_query = "INSERT INTO league_members (league_id, user_id) VALUES (?, ?)";
    $stmt3 = $conn->prepare($insert_member_query);
    $stmt3->bind_param("ii", $league_id, $user_id);
    
    if (!$stmt3->execute()) {
        throw new Exception("Erro ao entrar na liga: " . $stmt3->error);
    }
    
    $_SESSION['league_message'] = 'Você entrou com sucesso na liga "' . htmlspecialchars($league['name']) . '"!';
    
} catch (Exception $e) {
    $_SESSION['league_error'] = 'Erro ao entrar na liga: ' . $e->getMessage();
}

$conn->close();
header('Location: ../../leagues.php');
exit;
?>
