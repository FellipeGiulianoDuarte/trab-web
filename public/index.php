<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type the colour, not the word</title>
    <link rel="icon" type="image/png" href="icon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
            text-align: center;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            margin-bottom: 30px;
        }
        .nav-links a {
            display: block;
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            margin: 10px 0;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: #0056b3;
        }
        .logout-link {
            background-color: #dc3545 !important; /* Important to override general link style */
        }
        .logout-link:hover {
            background-color: #c82333 !important;
        }
    </style>
</head>
<body>    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h1>
                Bem-vindo, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Usuário'; ?>!
            </h1>
            <p>O que você gostaria de fazer?</p>
            <div class="nav-links">
                <a href="game_page.php">Jogar</a>
                <a href="scores.php">Ver Pontuações</a>
                <a href="leagues.php">Ver Ligas</a>
                <a href="backend/auth/logout.php" class="logout-link">Sair</a> <!-- Direct path to backend logout handler -->
            </div>
        <?php else: ?>
            <p>Por favor, faça login ou cadastre-se para continuar.</p>
            <div class="nav-links">
                <a href="login.php">Entrar</a>
                <a href="register.php">Cadastrar</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
