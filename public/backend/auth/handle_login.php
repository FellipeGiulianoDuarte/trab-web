<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db/connection.php'; // Step 1 & 2
require_once __DIR__ . '/session_manager.php'; // Include session manager

// Step 3: Check if the request method is POST
// This check is relevant if the script is directly accessed,
// but less so when included by login.php which already handles form submission.
// However, keeping it provides an extra layer if direct access is attempted.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../login.php"); // Corrected path
    exit();
}

// Step 4: Retrieve data from $_POST
$login_identifier = $_POST['login_identifier'] ?? '';
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

// Step 5: Validate inputs
if (empty($login_identifier) || empty($password)) {
    $_SESSION['error_message'] = "Both username/email and password are required.";
    header("Location: ../../login.php"); // Corrected path
    exit();
}

// Step 6: Fetch user from database
// Assuming the password column in your database is named 'password' and stores the hash.
// If it's 'password_hash', change $user['password'] to $user['password_hash'] below.
$sql = "SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?"; // Changed 'password' to 'password_hash'
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle database error (prepare failed)
    $_SESSION['error_message'] = "Database error. Please try again later. (Code: P1)";
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error); // Log the error
    header("Location: ../../login.php"); // Corrected path
    exit();
}

$stmt->bind_param("ss", $login_identifier, $login_identifier);

if (!$stmt->execute()) {
    // Handle database error (execute failed)
    $_SESSION['error_message'] = "Database error. Please try again later. (Code: E1)";
    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error); // Log the error
    $stmt->close();
    $conn->close();
    header("Location: ../../login.php"); // Corrected path
    exit();
}

$result = $stmt->get_result();

// Step 7: Verify credentials
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();    // Verify password (ensure your password column in DB is named 'password' or adjust as needed)
    if (password_verify($password, $user['password_hash'])) { // Changed $user['password'] to $user['password_hash']
        // Password is correct - use SessionManager to create session
        SessionManager::createSession($user['id'], $user['username'], $remember_me);
        
        // Redirect to a protected page (e.g., index.php or a game page)
        header("Location: ../../index.php"); // Corrected path
        $stmt->close();
        $conn->close();
        exit();} else {
        // Invalid password
        $_SESSION['error_message'] = "Invalid credentials.";
        $stmt->close();
        $conn->close();
        header("Location: ../../login.php"); // Corrected path
        exit();
    }
} else {
    // No user found or multiple users (should not happen with unique constraints)
    $_SESSION['error_message'] = "Invalid credentials.";
    $stmt->close();
    $conn->close();
    header("Location: ../../login.php"); // Corrected path
    exit();
}

// Should not be reached if logic is correct, but as a fallback:
$stmt->close();
$conn->close();
$_SESSION['error_message'] = "An unexpected error occurred.";
header("Location: ../../login.php"); // Corrected path
exit();
?>
