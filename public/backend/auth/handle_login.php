<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/session_manager.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../login.php");
    exit();
}

$login_identifier = $_POST['login_identifier'] ?? '';
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

if (empty($login_identifier) || empty($password)) {
    $_SESSION['error_message'] = "Nome de usuário/email e senha são obrigatórios.";
    header("Location: ../../login.php");
    exit();
}

$sql = "SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $_SESSION['error_message'] = "Erro no banco de dados. Tente novamente mais tarde. (Código: P1)";
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    header("Location: ../../login.php");
    exit();
}

$stmt->bind_param("ss", $login_identifier, $login_identifier);

if (!$stmt->execute()) {
    $_SESSION['error_message'] = "Erro no banco de dados. Tente novamente mais tarde. (Código: E1)";
    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    $stmt->close();
    $conn->close();
    header("Location: ../../login.php");
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password_hash'])) {
        SessionManager::createSession($user['id'], $user['username'], $remember_me);
        
        header("Location: ../../index.php");
        $stmt->close();
        $conn->close();        exit();} else {
        $_SESSION['error_message'] = "Credenciais inválidas.";
        $stmt->close();
        $conn->close();
        header("Location: ../../login.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Credenciais inválidas.";
    $stmt->close();
    $conn->close();
    header("Location: ../../login.php");
    exit();
}

$stmt->close();
$conn->close();
$_SESSION['error_message'] = "Ocorreu um erro inesperado.";
header("Location: ../../login.php");
exit();
?>
