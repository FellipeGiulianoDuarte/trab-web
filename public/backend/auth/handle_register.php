<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db/connection.php';

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Formato de email inválido.";
    header("Location: ../../register.php");
    exit();
}

if (strlen($username) > 50) {
    $_SESSION['error_message'] = "Nome de usuário não pode exceder 50 caracteres.";
    header("Location: ../../register.php");
    exit();
}
if (strlen($email) > 254) {
    $_SESSION['error_message'] = "Email não pode exceder 254 caracteres.";
    header("Location: ../../register.php");
    exit();
}
if (strlen($password) > 72) { 
    $_SESSION['error_message'] = "Senha não pode exceder 72 caracteres.";
    header("Location: ../../register.php");
    exit();
}

if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
    header("Location: ../../register.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error_message'] = "As senhas não coincidem.";
    header("Location: ../../register.php");
    exit();
}

$sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
$stmt_check = $conn->prepare($sql_check);
if ($stmt_check === false) {
    error_log("Database error (prepare failed): " . $conn->error);
    $_SESSION['error_message'] = "Ocorreu um erro inesperado. Tente novamente mais tarde.";
    header("Location: ../../register.php");
    exit();
}

$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['error_message'] = "Nome de usuário ou email já existe.";
    $stmt_check->close();
    header("Location: ../../register.php");
    exit();
}
$stmt_check->close();

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql_insert = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
if ($stmt_insert === false) {
    $_SESSION['error_message'] = "Erro no banco de dados (falha na preparação para inserção): " . $conn->error;
    header("Location: ../../register.php");
    exit();
}

$stmt_insert->bind_param("sss", $username, $email, $password_hash);

if ($stmt_insert->execute()) {
    $_SESSION['success_message'] = "Cadastro realizado com sucesso! Por favor, faça login.";
    $stmt_insert->close();
    $conn->close();
    header("Location: ../../login.php");
    exit();
} else {
    $_SESSION['error_message'] = "Falha no cadastro. Tente novamente. Erro: " . $stmt_insert->error;
    $stmt_insert->close();
    $conn->close();
    header("Location: ../../register.php");
    exit();
}

?>
