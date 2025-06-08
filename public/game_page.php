<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type the Colour, Not the Word</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>    <div class="container">
        <h1>Type the Colour, Not the Word</h1>
        <div class="game-instructions">
            <p><strong>Como jogar:</strong></p>
            <ul>
                <li>Uma palavra de cor aparecerá na tela pintada de uma cor diferente</li>
                <li>Digite o nome da <strong>COR</strong> em que a palavra está pintada</li>
                <li>Não digite a palavra que está escrita!</li>
                <li>Você tem 60 segundos para fazer o máximo de pontos</li>
                <li>Cada resposta correta vale 10 pontos</li>
            </ul>
        </div>
        
        <div class="game-area">
            <div class="game-info">
                <span>Pontuação: <span id="score">0</span></span>
                <span>Tempo: <span id="timer">60</span>s</span>
            </div>
            
            <div id="word-display">Clique em "Iniciar" para começar!</div>
            
            <input type="text" id="color-input" placeholder="Digite a cor aqui..." disabled>
            
            <div class="game-controls">
                <button id="start-button">Iniciar</button>
                <button id="restart-button" style="display: none;">Jogar Novamente</button>
            </div>
            
            <div id="game-over-message" style="display: none;"></div>
        </div>
        
        <div class="nav-links">
            <a href="index.php">Voltar ao Menu</a>
            <a href="scores.php">Ver Pontuações</a>
        </div>
    </div>
    
    <script src="js/game.js"></script>
</body>
</html>
