<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../leagues.php');
    exit;
}

$league_name = trim($_POST['league_name'] ?? '');
$keyword = trim($_POST['keyword'] ?? '');
$creator_user_id = $_SESSION['user_id'];

if (empty($league_name) || empty($keyword)) {
    $_SESSION['league_error'] = 'Nome da liga e palavra-chave são obrigatórios.';
    header('Location: ../../leagues.php');
    exit;
}

if (strlen($league_name) < 3) {
    $_SESSION['league_error'] = 'Nome da liga deve ter pelo menos 3 caracteres.';
    header('Location: ../../leagues.php');
    exit;
}

if (strlen($keyword) < 3) {
    $_SESSION['league_error'] = 'Palavra-chave deve ter pelo menos 3 caracteres.';
    header('Location: ../../leagues.php');
    exit;
}

if (strlen($league_name) > 255) {
    $_SESSION['league_error'] = 'Nome da liga deve ter no máximo 255 caracteres.';
    header('Location: ../../leagues.php');
    exit;
}

if (strlen($keyword) > 50) {
    $_SESSION['league_error'] = 'Palavra-chave deve ter no máximo 50 caracteres.';
    header('Location: ../../leagues.php');
    exit;
}

// Check if league name already exists (globally unique)
$check_name_query = "SELECT id FROM leagues WHERE name = ?";
$stmt = $conn->prepare($check_name_query);
$stmt->bind_param("s", $league_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['league_error'] = 'Já existe uma liga com este nome. Por favor, escolha um nome diferente.';
    header('Location: ../../leagues.php');
    exit;
}

try {
    $conn->begin_transaction();
    
    $insert_league_query = "INSERT INTO leagues (name, creator_user_id, keyword) VALUES (?, ?, ?)";
    $stmt2 = $conn->prepare($insert_league_query);
    $stmt2->bind_param("sis", $league_name, $creator_user_id, $keyword);
    
    if (!$stmt2->execute()) {
        throw new Exception("Erro ao criar a liga: " . $stmt2->error);
    }
    
    $league_id = $conn->insert_id;
    
    $insert_member_query = "INSERT INTO league_members (league_id, user_id) VALUES (?, ?)";
    $stmt3 = $conn->prepare($insert_member_query);
    $stmt3->bind_param("ii", $league_id, $creator_user_id);
    
    if (!$stmt3->execute()) {
        throw new Exception("Erro ao adicionar criador como membro: " . $stmt3->error);
    }
    
    $conn->commit();
    
    $_SESSION['league_message'] = 'Liga "' . htmlspecialchars($league_name) . '" criada com sucesso!';
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['league_error'] = 'Erro ao criar a liga: ' . $e->getMessage();
}

$conn->close();
header('Location: ../../leagues.php');
exit;
?>
