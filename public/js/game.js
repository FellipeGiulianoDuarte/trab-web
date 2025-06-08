// Game variables
const colors = ['vermelho', 'azul', 'verde', 'amarelo', 'laranja', 'roxo', 'rosa', 'preto', 'branco', 'cinza'];
const colorMapping = {
    'vermelho': 'red',
    'azul': 'blue',
    'verde': 'green',
    'amarelo': 'yellow',
    'laranja': 'orange',
    'roxo': 'purple',
    'rosa': 'pink',
    'preto': 'black',
    'branco': 'white',
    'cinza': 'gray'
};

// Color variations for better contrast
const colorVariations = {
    'vermelho': '#dc3545',
    'azul': '#007bff',
    'verde': '#28a745',
    'amarelo': '#ffc107',
    'laranja': '#fd7e14',
    'roxo': '#6f42c1',
    'rosa': '#e83e8c',
    'preto': '#343a40',
    'branco': '#f8f9fa',
    'cinza': '#6c757d'
};

let score = 0;
let timeLeft = 60;
let isPlaying = false;
let gameTimer = null;
let currentCorrectColor = '';

// DOM elements
const wordDisplay = document.getElementById('word-display');
const colorInput = document.getElementById('color-input');
const scoreElement = document.getElementById('score');
const timerElement = document.getElementById('timer');
const startButton = document.getElementById('start-button');
const restartButton = document.getElementById('restart-button');
const gameOverMessage = document.getElementById('game-over-message');

// Event listeners
startButton.addEventListener('click', startGame);
restartButton.addEventListener('click', resetGame);
colorInput.addEventListener('keyup', handleInput);

function startGame() {
    // Reset game state
    score = 0;
    timeLeft = 60;
    isPlaying = true;
    
    // Update UI
    scoreElement.textContent = score;
    timerElement.textContent = timeLeft;
    startButton.style.display = 'none';
    restartButton.style.display = 'none';
    colorInput.disabled = false;
    colorInput.focus();
    gameOverMessage.style.display = 'none';
    
    // Start timer
    gameTimer = setInterval(updateTimer, 1000);
    
    // Generate first word
    generateWord();
}

function updateTimer() {
    timeLeft--;
    timerElement.textContent = timeLeft;
    
    if (timeLeft <= 0) {
        endGame();
    }
}

function generateWord() {
    if (!isPlaying) return;
    
    // Choose random word and color, ensuring they're different
    let wordIndex, colorIndex;
    
    do {
        wordIndex = Math.floor(Math.random() * colors.length);
        colorIndex = Math.floor(Math.random() * colors.length);
    } while (wordIndex === colorIndex);
    
    const displayWord = colors[wordIndex];
    const displayColor = colors[colorIndex];
    currentCorrectColor = displayColor;
    
    // Update the display with better colors for visibility
    wordDisplay.textContent = displayWord.toUpperCase();
    const colorValue = colorVariations[displayColor] || colorMapping[displayColor];
    wordDisplay.style.color = colorValue;
    wordDisplay.style.fontSize = '3em';
    wordDisplay.style.fontWeight = 'bold';
    wordDisplay.style.textShadow = '2px 2px 4px rgba(0,0,0,0.3)';
    
    // Special handling for white text (add dark background)
    if (displayColor === 'branco') {
        wordDisplay.style.backgroundColor = '#343a40';
        wordDisplay.style.padding = '10px';
        wordDisplay.style.borderRadius = '8px';
    } else {
        wordDisplay.style.backgroundColor = 'transparent';
        wordDisplay.style.padding = '0';
    }
}

function handleInput(event) {
    if (!isPlaying) return;
    
    // Check for submit keys: Enter or Space (removed Backspace)
    if (event.key === 'Enter' || event.key === ' ') {
        // Prevent default behavior for space
        if (event.key === ' ') {
            event.preventDefault();
        }
        
        const userAnswer = colorInput.value.toLowerCase().trim();
        
        // Only process if there's an answer
        if (userAnswer) {
            if (userAnswer === currentCorrectColor) {
                // Correct answer
                score += 10;
                scoreElement.textContent = score;
                
                // Visual feedback for correct answer
                wordDisplay.style.backgroundColor = '#4CAF50';
                setTimeout(() => {
                    wordDisplay.style.backgroundColor = 'transparent';
                }, 200);
            } else {
                // Wrong answer - visual feedback
                wordDisplay.style.backgroundColor = '#f44336';
                setTimeout(() => {
                    wordDisplay.style.backgroundColor = 'transparent';
                }, 200);
            }
            
            // Clear input and generate new word
            colorInput.value = '';
            generateWord();
        }
    }
}

function endGame() {
    isPlaying = false;
    clearInterval(gameTimer);
    
    // Disable input
    colorInput.disabled = true;
    
    // Show game over message
    gameOverMessage.innerHTML = `
        <h2>Fim de Jogo!</h2>
        <p>Sua pontuação final: <strong>${score}</strong> pontos</p>
        <p>Salvando pontuação...</p>
    `;
    gameOverMessage.style.display = 'block';
    
    // Show restart button
    restartButton.style.display = 'inline-block';
    
    // Save score to backend
    saveScore();
}

function resetGame() {
    // Reset display
    wordDisplay.textContent = 'Clique em "Iniciar" para começar!';
    wordDisplay.style.color = '#333';
    wordDisplay.style.backgroundColor = 'transparent';
    colorInput.value = '';
    
    // Show start button again
    startButton.style.display = 'inline-block';
    restartButton.style.display = 'none';
    gameOverMessage.style.display = 'none';
}

async function saveScore() {
    try {
        const response = await fetch('backend/game_logic/save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                score: score
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            gameOverMessage.innerHTML = `
                <h2>Fim de Jogo!</h2>
                <p>Sua pontuação final: <strong>${score}</strong> pontos</p>
                <p style="color: green;">✓ Pontuação salva com sucesso!</p>
            `;
        } else {
            gameOverMessage.innerHTML = `
                <h2>Fim de Jogo!</h2>
                <p>Sua pontuação final: <strong>${score}</strong> pontos</p>
                <p style="color: red;">✗ Erro ao salvar pontuação: ${result.message || 'Erro desconhecido'}</p>
            `;
        }
    } catch (error) {
        console.error('Erro ao salvar pontuação:', error);
        gameOverMessage.innerHTML = `
            <h2>Fim de Jogo!</h2>
            <p>Sua pontuação final: <strong>${score}</strong> pontos</p>
            <p style="color: red;">✗ Erro de conexão ao salvar pontuação</p>
        `;
    }
}

// Initialize game display
wordDisplay.style.color = '#333';
wordDisplay.style.fontSize = '2em';
wordDisplay.style.minHeight = '80px';
wordDisplay.style.display = 'flex';
wordDisplay.style.alignItems = 'center';
wordDisplay.style.justifyContent = 'center';
