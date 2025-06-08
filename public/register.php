<?php 
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
        input[type="email"],
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
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
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
        .alert-success { /* Added for completeness, though less likely used here */
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo htmlspecialchars($_SESSION['error_message']); 
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success_message']); 
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <h2>Register</h2>
        <form action="backend/auth/handle_register.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required maxlength="50">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required maxlength="254">
                <div id="email-error" class="error-message"></div>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required maxlength="72">
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required maxlength="72">
                <div id="password-match-error" class="error-message"></div>
            </div>
            <div>
                <input type="submit" value="Register">
            </div>
        </form>
    </div>
    <script>
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatchError = document.getElementById('password-match-error');

        emailInput.addEventListener('input', () => {
            if (emailInput.validity.typeMismatch || !isValidEmail(emailInput.value)) {
                emailError.textContent = 'Please enter a valid email address.';
            } else {
                emailError.textContent = '';
            }
        });

        function checkPasswordMatch() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                passwordMatchError.textContent = 'Passwords do not match.';
                return false;
            } else {
                passwordMatchError.textContent = '';
                return true;
            }
        }

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        passwordInput.addEventListener('input', checkPasswordMatch);

        form.addEventListener('submit', (event) => {
            let isValid = true;
            if (!isValidEmail(emailInput.value)) {
                emailError.textContent = 'Please enter a valid email address.';
                isValid = false;
            }
            if (!checkPasswordMatch()) {
                isValid = false;
            }            if (!isValid) {
                event.preventDefault();
            }
        });

        function isValidEmail(email) {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            return emailRegex.test(email);
        }
    </script>
</body>
</html>
