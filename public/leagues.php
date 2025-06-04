<?php
require_once __DIR__ . '/../src/auth/auth_check.php';
require_once __DIR__ . '/../src/db/connection.php';

$message = '';
$error = '';

// Handle success/error messages from POST requests
if (isset($_SESSION['league_message'])) {
    $message = $_SESSION['league_message'];
    unset($_SESSION['league_message']);
}
if (isset($_SESSION['league_error'])) {
    $error = $_SESSION['league_error'];
    unset($_SESSION['league_error']);
}

// Get current user ID
$user_id = $_SESSION['user_id'];

// Fetch available leagues
$leagues_query = "SELECT l.id, l.name, l.created_at, u.username as creator_name,
                  COUNT(lm.user_id) as member_count,
                  COUNT(CASE WHEN lm.user_id = ? THEN 1 END) as is_member
                  FROM leagues l
                  JOIN users u ON l.creator_user_id = u.id
                  LEFT JOIN league_members lm ON l.id = lm.league_id
                  GROUP BY l.id, l.name, l.created_at, u.username
                  ORDER BY l.created_at DESC";
$stmt = $conn->prepare($leagues_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$leagues_result = $stmt->get_result();

// Fetch user's leagues
$user_leagues_query = "SELECT l.id, l.name, l.keyword, l.created_at, u.username as creator_name,
                       (SELECT COUNT(*) FROM league_members lm WHERE lm.league_id = l.id) as member_count
                       FROM leagues l 
                       JOIN users u ON l.creator_user_id = u.id 
                       JOIN league_members lm ON l.id = lm.league_id 
                       WHERE lm.user_id = ? 
                       ORDER BY lm.joined_at DESC";
$stmt2 = $conn->prepare($user_leagues_query);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$user_leagues_result = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ligas - Jogo de Digitação</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .leagues-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .league-section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .league-form {
            display: grid;
            gap: 15px;
            max-width: 400px;
        }
        .league-form input, .league-form button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .league-form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .league-form button:hover {
            background-color: #0056b3;
        }
        .league-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .league-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .league-info p {
            margin: 2px 0;
            color: #666;
            font-size: 14px;
        }
        .league-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .join-form {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }        .join-form input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 150px;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }
        .crown-icon {
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .league-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .league-actions {
                margin-top: 15px;
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }
            .join-form {
                width: 100%;
                margin-top: 10px;
            }
            .join-form input {
                width: calc(100% - 80px);
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="leagues-container">
        <h1>Gerenciamento de Ligas</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>        <!-- Create League Section -->
        <div class="league-section" id="create-league">
            <h2>Criar Nova Liga</h2>
            <form action="../src/league_logic/create_league.php" method="POST" class="league-form" onsubmit="return validateLeagueForm();">
                <input type="text" name="league_name" id="league_name" placeholder="Nome da Liga" required maxlength="255" minlength="3">
                <input type="text" name="keyword" id="keyword" placeholder="Palavra-chave (para entrada)" required maxlength="50" minlength="3" autocomplete="off">
                <small style="color: #666;">A palavra-chave será necessária para outros jogadores entrarem na sua liga.</small>
                <button type="submit">Criar Liga</button>
            </form>
        </div>

        <script>
        function validateLeagueForm() {
            const name = document.getElementById('league_name').value.trim();
            const keyword = document.getElementById('keyword').value.trim();
            
            if (name.length < 3) {
                alert('O nome da liga deve ter pelo menos 3 caracteres.');
                return false;
            }
            
            if (keyword.length < 3) {
                alert('A palavra-chave deve ter pelo menos 3 caracteres.');
                return false;
            }
            
            if (keyword.includes(' ')) {
                alert('A palavra-chave não deve conter espaços.');
                return false;
            }
            
            return confirm('Criar liga "' + name + '" com a palavra-chave "' + keyword + '"?');
        }
        </script>

        <!-- My Leagues Section -->
        <div class="league-section">
            <h2>Minhas Ligas</h2>
            <?php if ($user_leagues_result->num_rows > 0): ?>
                <?php while ($league = $user_leagues_result->fetch_assoc()): ?>
                    <div class="league-item">
                        <div class="league-info">
                            <h4><?php echo htmlspecialchars($league['name']); ?></h4>
                            <p><strong>Criador:</strong> <?php echo htmlspecialchars($league['creator_name']); ?></p>
                            <p><strong>Membros:</strong> <?php echo $league['member_count']; ?></p>
                            <p><strong>Criada em:</strong> <?php echo date('d/m/Y H:i', strtotime($league['created_at'])); ?></p>
                            <?php if ($league['creator_name'] === $_SESSION['username']): ?>
                                <p><strong>Palavra-chave:</strong> <code><?php echo htmlspecialchars($league['keyword']); ?></code></p>
                            <?php endif; ?>
                        </div>                        <div class="league-actions">                            <a href="league_scores.php?id=<?php echo $league['id']; ?>" class="btn btn-primary">Ver Placar</a>                            <?php if ($league['creator_name'] !== $_SESSION['username']): ?>
                                <form action="../src/league_logic/leave_league.php" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja sair desta liga?');">
                                    <input type="hidden" name="league_id" value="<?php echo $league['id']; ?>">
                                    <button type="submit" class="btn btn-secondary">Sair da Liga</button>
                                </form>
                            <?php else: ?>
                                <span class="btn btn-success" title="Você é o criador desta liga">👑 Criador</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Você ainda não participa de nenhuma liga.</p>
            <?php endif; ?>
        </div>        <!-- Available Leagues Section -->
        <div class="league-section">
            <h2>Ligas Disponíveis</h2>
            <?php if ($leagues_result->num_rows > 0): ?>
                <?php while ($league = $leagues_result->fetch_assoc()): ?>
                    <div class="league-item">
                        <div class="league-info">
                            <h4><?php echo htmlspecialchars($league['name']); ?></h4>
                            <p><strong>Criador:</strong> <?php echo htmlspecialchars($league['creator_name']); ?></p>
                            <p><strong>Membros:</strong> <?php echo $league['member_count']; ?></p>
                            <p><strong>Criada em:</strong> <?php echo date('d/m/Y H:i', strtotime($league['created_at'])); ?></p>
                        </div>
                        <div class="league-actions">                            <?php if ($league['is_member'] > 0): ?>
                                <span class="btn btn-success">✓ Membro</span>
                            <?php else: ?>
                                <form action="../src/league_logic/join_league.php" method="POST" class="join-form" onsubmit="return confirm('Tem certeza que deseja entrar nesta liga?');">
                                    <input type="hidden" name="league_id" value="<?php echo $league['id']; ?>">
                                    <label for="join_keyword_<?php echo $league['id']; ?>">Palavra-chave</label>
                                    <input type="text" id="join_keyword_<?php echo $league['id']; ?>" name="keyword" placeholder="Palavra-chave" required maxlength="50" autocomplete="off">
                                    <button type="submit" class="btn btn-primary">Entrar</button>
                                </form>
                            <?php endif; ?>
                            <a href="league_scores.php?id=<?php echo $league['id']; ?>" class="btn btn-secondary">Ver Placar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma liga disponível no momento. <a href="#create-league">Crie a primeira liga!</a></p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-secondary">Voltar ao Menu Principal</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
