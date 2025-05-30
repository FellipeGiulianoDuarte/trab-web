<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db/connection.php'; // Step 1: Include connection

// Step 4: Retrieve data from $_POST
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Step 5: Validate inputs
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Invalid email format.";
    header("Location: /register.php"); // Corrected path
    exit();
}

// Validate input lengths
if (strlen($username) > 50) {
    $_SESSION['error_message'] = "Username cannot exceed 50 characters.";
    header("Location: /register.php"); // Corrected path
    exit();
}
if (strlen($email) > 254) {
    $_SESSION['error_message'] = "Email cannot exceed 254 characters.";
    header("Location: /register.php"); // Corrected path
    exit();
}
// Passwords can be up to 72 characters due to bcrypt limitations
if (strlen($password) > 72) { 
    $_SESSION['error_message'] = "Password cannot exceed 72 characters.";
    header("Location: /register.php"); // Corrected path
    exit();
}

// Ensure all fields are filled
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['error_message'] = "All fields are required.";
    header("Location: /register.php"); // Corrected path
    exit();
}

// Check if password and confirm_password match
if ($password !== $confirm_password) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header("Location: /register.php"); // Corrected path
    exit();
}

// Step 6: Check for existing user
$sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
$stmt_check = $conn->prepare($sql_check);
if ($stmt_check === false) {
    // Handle error, e.g., log it or display a generic error message
    error_log("Database error (prepare failed): " . $conn->error);
    $_SESSION['error_message'] = "An unexpected error occurred. Please try again later.";
    header("Location: /register.php"); // Corrected path
    exit();
}

$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['error_message'] = "Username or email already exists.";
    $stmt_check->close();
    header("Location: /register.php"); // Corrected path
    exit();
}
$stmt_check->close();

// Step 7: Insert new user
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql_insert = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"; // Changed 'password' to 'password_hash'
$stmt_insert = $conn->prepare($sql_insert);
if ($stmt_insert === false) {
    // Handle error
    $_SESSION['error_message'] = "Database error (prepare failed for insert): " . $conn->error;
    header("Location: /register.php"); // Corrected path
    exit();
}

$stmt_insert->bind_param("sss", $username, $email, $password_hash);

if ($stmt_insert->execute()) {
    // Step 8: Redirect with success message
    $_SESSION['success_message'] = "Registration successful! Please log in.";
    $stmt_insert->close();
    $conn->close();
    header("Location: /login.php"); // Corrected path
    exit();
} else {
    // Step 8: Redirect with error message if insert fails
    $_SESSION['error_message'] = "Registration failed. Please try again. Error: " . $stmt_insert->error;
    $stmt_insert->close();
    $conn->close();
    header("Location: /register.php"); // Corrected path
    exit();
}

?>
