<?php

class AuthController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function login() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /login");
            exit();
        }

        // Retrieve data from $_POST
        $login_identifier = $_POST['login_identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($login_identifier) || empty($password)) {
            $_SESSION['error_message'] = "Both username/email and password are required.";
            header("Location: /login");
            exit();
        }

        try {
            // Check if the identifier is an email or username
            if (filter_var($login_identifier, FILTER_VALIDATE_EMAIL)) {
                // It's an email
                $sql = "SELECT id, username, email, password_hash FROM users WHERE email = ?";
            } else {
                // It's a username
                $sql = "SELECT id, username, email, password_hash FROM users WHERE username = ?";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $login_identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Redirect to main page
                    header("Location: /");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Invalid username/email or password.";
                }
            } else {
                $_SESSION['error_message'] = "Invalid username/email or password.";
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['error_message'] = "An error occurred during login. Please try again.";
        }

        header("Location: /login");
        exit();
    }
    
    public function register() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /register");
            exit();
        }

        // Get form data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $_SESSION['error_message'] = "All fields are required.";
            header("Location: /register");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Invalid email format.";
            header("Location: /register");
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['error_message'] = "Password must be at least 6 characters long.";
            header("Location: /register");
            exit();
        }

        if ($password !== $confirm_password) {
            $_SESSION['error_message'] = "Passwords do not match.";
            header("Location: /register");
            exit();
        }

        try {
            // Check if username already exists
            $check_username = "SELECT id FROM users WHERE username = ?";
            $stmt = $this->conn->prepare($check_username);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $_SESSION['error_message'] = "Username already exists.";
                header("Location: /register");
                exit();
            }

            // Check if email already exists
            $check_email = "SELECT id FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($check_email);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $_SESSION['error_message'] = "Email already exists.";
                header("Location: /register");
                exit();
            }

            // Hash password and create user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_user = "INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($insert_user);
            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Registration successful! Please log in.";
                header("Location: /login");
            } else {
                $_SESSION['error_message'] = "Registration failed. Please try again.";
                header("Location: /register");
            }

        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $_SESSION['error_message'] = "An error occurred during registration. Please try again.";
            header("Location: /register");
        }
        
        exit();
    }
    
    public function logout() {
        // Destroy all session data
        session_destroy();
        
        // Redirect to login page
        header("Location: /login");
        exit();
    }
}
