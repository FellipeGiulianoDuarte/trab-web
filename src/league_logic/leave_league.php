<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/leagues.php');
    exit;
}

// Get form data
$league_id = intval($_POST['league_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Validate input
if ($league_id <= 0) {
    $_SESSION['league_error'] = 'ID da liga inválido.';
    header('Location: ../../public/leagues.php');
    exit;
}

// Check if league exists and get league info
$check_league_query = "SELECT l.id, l.name, l.creator_user_id FROM leagues l WHERE l.id = ?";
$stmt = $conn->prepare($check_league_query);
$stmt->bind_param("i", $league_id);
$stmt->execute();
$league_result = $stmt->get_result();

if ($league_result->num_rows === 0) {
    $_SESSION['league_error'] = 'Liga não encontrada.';
    header('Location: ../../public/leagues.php');
    exit;
}

$league = $league_result->fetch_assoc();

// Check if user is the creator of the league
if ($league['creator_user_id'] == $user_id) {
    $_SESSION['league_error'] = 'Você não pode sair de uma liga que você criou. Você deve transferir a propriedade ou excluir a liga.';
    header('Location: ../../public/leagues.php');
    exit;
}

// Check if user is a member
$check_member_query = "SELECT id FROM league_members WHERE league_id = ? AND user_id = ?";
$stmt2 = $conn->prepare($check_member_query);
$stmt2->bind_param("ii", $league_id, $user_id);
$stmt2->execute();
$member_result = $stmt2->get_result();

if ($member_result->num_rows === 0) {
    $_SESSION['league_error'] = 'Você não é membro desta liga.';
    header('Location: ../../public/leagues.php');
    exit;
}

try {
    // Remove user from league
    $delete_member_query = "DELETE FROM league_members WHERE league_id = ? AND user_id = ?";
    $stmt3 = $conn->prepare($delete_member_query);
    $stmt3->bind_param("ii", $league_id, $user_id);
    
    if (!$stmt3->execute()) {
        throw new Exception("Erro ao sair da liga: " . $stmt3->error);
    }
    
    $_SESSION['league_message'] = 'Você saiu da liga "' . htmlspecialchars($league['name']) . '" com sucesso.';
    
} catch (Exception $e) {
    $_SESSION['league_error'] = 'Erro ao sair da liga: ' . $e->getMessage();
}

$conn->close();
header('Location: ../../public/leagues.php');
exit;
?>
