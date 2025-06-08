<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
try {
    require_once __DIR__ . '/backend/auth/auth_check.php';
    require_once __DIR__ . '/backend/db/connection.php';
} catch (Exception $e) {
    die("System error: Unable to load required components. " . $e->getMessage());
}

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error");
}

// Verify user session
if (!isset($_SESSION['user_id'])) {
    die("User session error");
}

$user_id = $_SESSION['user_id'];

// Initialize variables
$userGames = [];
$userTotalScore = 0;
$userWeeklyScore = 0;
$userHighestScore = 0;
$globalLeaderboard = [];
$weeklyLeaderboard = [];

try {
    // Get user's personal scores
    $stmt = $conn->prepare("
        SELECT score, played_at 
        FROM games 
        WHERE user_id = ? 
        ORDER BY played_at DESC 
        LIMIT 10
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare user games query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute user games query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $userGames = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get user's total score
    $stmt = $conn->prepare("SELECT SUM(score) as total_score FROM games WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare total score query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute total score query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $userTotal = $result->fetch_assoc();
    $userTotalScore = $userTotal['total_score'] ?? 0;
    $stmt->close();

    // Get user's weekly score (last 7 days)
    $stmt = $conn->prepare("
        SELECT SUM(score) as weekly_score 
        FROM games 
        WHERE user_id = ? AND played_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare weekly score query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute weekly score query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $userWeekly = $result->fetch_assoc();    $userWeeklyScore = $userWeekly['weekly_score'] ?? 0;
    $stmt->close();

    // Get user's highest score
    $stmt = $conn->prepare("SELECT MAX(score) as highest_score FROM games WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare highest score query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute highest score query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $userHighest = $result->fetch_assoc();
    $userHighestScore = $userHighest['highest_score'] ?? 0;
    $stmt->close();    // Get global leaderboard (total scores)
    $stmt = $conn->prepare("
        SELECT u.username, SUM(g.score) as total_score, MAX(g.score) as highest_score
        FROM users u
        JOIN games g ON u.id = g.user_id
        GROUP BY u.id, u.username
        ORDER BY total_score DESC
        LIMIT 10
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare global leaderboard query: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute global leaderboard query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $globalLeaderboard = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get weekly leaderboard
    $stmt = $conn->prepare("
        SELECT u.username, SUM(g.score) as weekly_score
        FROM users u
        JOIN games g ON u.id = g.user_id
        WHERE g.played_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY u.id, u.username
        ORDER BY weekly_score DESC
        LIMIT 10
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare weekly leaderboard query: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute weekly leaderboard query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $weeklyLeaderboard = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

} catch (Exception $e) {
    error_log("Scores page error: " . $e->getMessage());
    die("Error loading scores: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pontuações - Type the Colour</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Pontuações</h1>
        
        <!-- User's Personal Stats -->
        <div class="score-section">
            <h2>Suas Estatísticas</h2>            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Pontuação Total</h3>
                    <p class="stat-number"><?php echo number_format($userTotalScore); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pontuação Semanal</h3>
                    <p class="stat-number"><?php echo number_format($userWeeklyScore); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Maior Pontuação</h3>
                    <p class="stat-number"><?php echo number_format($userHighestScore); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Jogos Jogados</h3>
                    <p class="stat-number"><?php echo count($userGames); ?></p>
                </div>
            </div>
        </div>

        <!-- User's Recent Games -->
        <div class="score-section">
            <h2>Suas Últimas Partidas</h2>
            <?php if (empty($userGames)): ?>
                <p>Você ainda não jogou nenhuma partida.</p>
            <?php else: ?>
                <table class="scores-table">
                    <thead>
                        <tr>
                            <th>Pontuação</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userGames as $game): ?>
                            <tr>
                                <td><?php echo number_format($game['score']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($game['played_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Global Leaderboard -->
        <div class="score-section">
            <h2>Ranking Geral</h2>
            <?php if (empty($globalLeaderboard)): ?>
                <p>Nenhuma pontuação registrada ainda.</p>
            <?php else: ?>                <table class="scores-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Jogador</th>
                            <th>Pontuação Total</th>
                            <th>Maior Pontuação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($globalLeaderboard as $index => $player): ?>
                            <tr <?php echo $player['username'] === $_SESSION['username'] ? 'class="highlight-user"' : ''; ?>>
                                <td><?php echo $index + 1; ?>º</td>
                                <td><?php echo htmlspecialchars($player['username']); ?></td>
                                <td><?php echo number_format($player['total_score']); ?></td>
                                <td><?php echo number_format($player['highest_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Weekly Leaderboard -->
        <div class="score-section">
            <h2>Ranking Semanal</h2>
            <?php if (empty($weeklyLeaderboard)): ?>
                <p>Nenhuma pontuação desta semana ainda.</p>
            <?php else: ?>
                <table class="scores-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Jogador</th>
                            <th>Pontuação Semanal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weeklyLeaderboard as $index => $player): ?>
                            <tr <?php echo $player['username'] === $_SESSION['username'] ? 'class="highlight-user"' : ''; ?>>
                                <td><?php echo $index + 1; ?>º</td>
                                <td><?php echo htmlspecialchars($player['username']); ?></td>
                                <td><?php echo number_format($player['weekly_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <a href="game_page.php">Jogar Novamente</a>
            <a href="index.php">Voltar ao Menu</a>
        </div>
    </div>
</body>
</html>
