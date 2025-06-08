<?php
require_once __DIR__ . '/backend/auth/auth_check.php';
require_once __DIR__ . '/backend/db/connection.php';

// Validar e sanitizar o ID da liga
$league_id = htmlspecialchars(intval($_GET['id'] ?? 0), ENT_QUOTES, 'UTF-8');
$user_id = $_SESSION['user_id'];

if ($league_id <= 0) {
    header('Location: leagues.php');
    exit;
}

$league_query = "SELECT l.id, l.name, l.created_at, u.username as creator_name,
                 (SELECT COUNT(*) FROM league_members lm WHERE lm.league_id = l.id) as member_count
                 FROM leagues l 
                 JOIN users u ON l.creator_user_id = u.id 
                 WHERE l.id = ?";
$stmt = $conn->prepare($league_query);
$stmt->bind_param("i", $league_id);
$stmt->execute();
$league_result = $stmt->get_result();

if ($league_result->num_rows === 0) {
    header('Location: leagues.php');
    exit;
}

$league = $league_result->fetch_assoc();

$member_check_query = "SELECT id FROM league_members WHERE league_id = ? AND user_id = ?";
$stmt2 = $conn->prepare($member_check_query);
$stmt2->bind_param("ii", $league_id, $user_id);
$stmt2->execute();
$is_member = $stmt2->get_result()->num_rows > 0;

$leaderboard_query = "SELECT u.username,
                      SUM(g.score) as total_score,
                      COUNT(g.id) as games_played,
                      MAX(g.score) as best_score,
                      AVG(g.score) as avg_score
                      FROM league_members lm
                      JOIN users u ON lm.user_id = u.id
                      LEFT JOIN games g ON u.id = g.user_id
                      WHERE lm.league_id = ?
                      GROUP BY u.id, u.username
                      ORDER BY total_score DESC, games_played DESC";
$stmt3 = $conn->prepare($leaderboard_query);
$stmt3->bind_param("i", $league_id);
$stmt3->execute();
$leaderboard_result = $stmt3->get_result();

$weekly_query = "SELECT u.username,
                 SUM(g.score) as weekly_score,
                 COUNT(g.id) as weekly_games,
                 MAX(g.score) as weekly_best
                 FROM league_members lm
                 JOIN users u ON lm.user_id = u.id
                 LEFT JOIN games g ON u.id = g.user_id AND g.played_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 WHERE lm.league_id = ?
                 GROUP BY u.id, u.username
                 ORDER BY weekly_score DESC, weekly_games DESC";
$stmt4 = $conn->prepare($weekly_query);
$stmt4->bind_param("i", $league_id);
$stmt4->execute();
$weekly_result = $stmt4->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placar da Liga: <?php echo htmlspecialchars($league['name']); ?></title>
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .league-scores-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .league-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .scores-section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .leaderboard-table th,
        .leaderboard-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .leaderboard-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .leaderboard-table tr:hover {
            background-color: #f5f5f5;
        }
        .rank {
            font-weight: bold;
            color: #007bff;
        }
        .rank.gold { color: #ffd700; }
        .rank.silver { color: #c0c0c0; }
        .rank.bronze { color: #cd7f32; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .member-badge {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="league-scores-container">
        <!-- League Information -->
        <div class="league-info">
            <h1><?php echo htmlspecialchars($league['name']); ?></h1>
            <p><strong>Criador:</strong> <?php echo htmlspecialchars($league['creator_name']); ?></p>
            <p><strong>Membros:</strong> <?php echo $league['member_count']; ?></p>
            <p><strong>Criada em:</strong> <?php echo date('d/m/Y H:i', strtotime($league['created_at'])); ?></p>
            <?php if ($is_member): ?>
                <span class="member-badge">Você é membro desta liga</span>
            <?php endif; ?>
        </div>

        <!-- Total Leaderboard -->
        <div class="scores-section">
            <h2>Placar Geral</h2>
            <?php if ($leaderboard_result->num_rows > 0): ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Jogador</th>
                            <th>Pontuação Total</th>
                            <th>Jogos</th>
                            <th>Melhor Pontuação</th>
                            <th>Média</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $position = 1;
                        while ($player = $leaderboard_result->fetch_assoc()): 
                            $rank_class = '';
                            if ($position == 1) $rank_class = 'gold';
                            elseif ($position == 2) $rank_class = 'silver';
                            elseif ($position == 3) $rank_class = 'bronze';
                        ?>
                            <tr>
                                <td><span class="rank <?php echo $rank_class; ?>"><?php echo $position; ?>º</span></td>
                                <td><?php echo htmlspecialchars($player['username']); ?></td>
                                <td><?php echo $player['total_score'] ?? 0; ?></td>
                                <td><?php echo $player['games_played'] ?? 0; ?></td>
                                <td><?php echo $player['best_score'] ?? '-'; ?></td>
                                <td><?php echo $player['avg_score'] ? number_format($player['avg_score'], 1) : '-'; ?></td>
                            </tr>
                        <?php 
                            $position++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum membro ainda jogou partidas.</p>
            <?php endif; ?>
        </div>

        <!-- Weekly Leaderboard -->
        <div class="scores-section">
            <h2>Placar Semanal (Últimos 7 dias)</h2>
            <?php if ($weekly_result->num_rows > 0): ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Jogador</th>
                            <th>Pontuação Semanal</th>
                            <th>Jogos</th>
                            <th>Melhor da Semana</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $position = 1;                        while ($player = $weekly_result->fetch_assoc()): 
                            if ($player['weekly_score'] == 0) continue;
                            $rank_class = '';
                            if ($position == 1) $rank_class = 'gold';
                            elseif ($position == 2) $rank_class = 'silver';
                            elseif ($position == 3) $rank_class = 'bronze';
                        ?>
                            <tr>
                                <td><span class="rank <?php echo $rank_class; ?>"><?php echo $position; ?>º</span></td>
                                <td><?php echo htmlspecialchars($player['username']); ?></td>
                                <td><?php echo $player['weekly_score'] ?? 0; ?></td>
                                <td><?php echo $player['weekly_games'] ?? 0; ?></td>
                                <td><?php echo $player['weekly_best'] ?? '-'; ?></td>
                            </tr>
                        <?php 
                            $position++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum membro jogou partidas esta semana.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="leagues.php" class="btn btn-secondary">Voltar às Ligas</a>
            <a href="index.php" class="btn btn-secondary">Menu Principal</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
