<?php 
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
            width: auto;
            height: auto;
        }
        .checkbox-container label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: center;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); 
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); 
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>        <form action="backend/auth/handle_login.php" method="POST">
            <div>
                <label for="login_identifier">Username or Email:</label>
                <input type="text" id="login_identifier" name="login_identifier" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox-container">
                <input type="checkbox" id="remember_me" name="remember_me" value="1">
                <label for="remember_me">Remember me for 7 days</label>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>
