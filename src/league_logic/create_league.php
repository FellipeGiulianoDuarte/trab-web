<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: leagues.php');
    exit;
}

// Get form data
$league_name = trim($_POST['league_name'] ?? '');
$keyword = trim($_POST['keyword'] ?? '');
$creator_user_id = $_SESSION['user_id'];

// Validate input
if (empty($league_name) || empty($keyword)) {
    $_SESSION['league_error'] = 'Nome da liga e palavra-chave são obrigatórios.';
    header('Location: leagues.php');
    exit;
}

// Validate minimum lengths
if (strlen($league_name) < 3) {
    $_SESSION['league_error'] = 'Nome da liga deve ter pelo menos 3 caracteres.';
    header('Location: leagues.php');
    exit;
}

if (strlen($keyword) < 3) {
    $_SESSION['league_error'] = 'Palavra-chave deve ter pelo menos 3 caracteres.';
    header('Location: leagues.php');
    exit;
}

// Validate league name length
if (strlen($league_name) > 255) {
    $_SESSION['league_error'] = 'Nome da liga deve ter no máximo 255 caracteres.';
    header('Location: leagues.php');
    exit;
}

// Validate keyword length and format
if (strlen($keyword) > 50) {
    $_SESSION['league_error'] = 'Palavra-chave deve ter no máximo 50 caracteres.';
    header('Location: leagues.php');
    exit;
}

// Check if keyword contains spaces
if (strpos($keyword, ' ') !== false) {
    $_SESSION['league_error'] = 'Palavra-chave não deve conter espaços.';
    header('Location: leagues.php');
    exit;
}

// Store raw inputs in the database. Ensure output is sanitized when rendering.

// Check if keyword already exists
$check_keyword_query = "SELECT id FROM leagues WHERE keyword = ?";
$stmt = $conn->prepare($check_keyword_query);
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['league_error'] = 'Esta palavra-chave já está sendo usada por outra liga. Por favor, escolha uma diferente.';
    header('Location: ../../public/leagues.php');
    exit;
}

// Check if user already has a league with this name
$check_name_query = "SELECT id FROM leagues WHERE name = ? AND creator_user_id = ?";
$stmt2 = $conn->prepare($check_name_query);
$stmt2->bind_param("si", $league_name, $creator_user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    $_SESSION['league_error'] = 'Você já possui uma liga com este nome.';
    header('Location: ../../public/leagues.php');
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();
    
    // Insert new league
    $insert_league_query = "INSERT INTO leagues (name, creator_user_id, keyword) VALUES (?, ?, ?)";
    $stmt3 = $conn->prepare($insert_league_query);
    $stmt3->bind_param("sis", $league_name, $creator_user_id, $keyword);
    
    if (!$stmt3->execute()) {
        throw new Exception("Erro ao criar a liga: " . $stmt3->error);
    }
    
    $league_id = $conn->insert_id;
    
    // Automatically add the creator as the first member
    $insert_member_query = "INSERT INTO league_members (league_id, user_id) VALUES (?, ?)";
    $stmt4 = $conn->prepare($insert_member_query);
    $stmt4->bind_param("ii", $league_id, $creator_user_id);
    
    if (!$stmt4->execute()) {
        throw new Exception("Erro ao adicionar criador como membro: " . $stmt4->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['league_message'] = 'Liga "' . htmlspecialchars($league_name) . '" criada com sucesso!';
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['league_error'] = 'Erro ao criar a liga: ' . $e->getMessage();
}

$conn->close();
header('Location: ../../public/leagues.php');
exit;
?>
