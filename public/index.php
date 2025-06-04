<?php
// Single Entry Point - Main Router
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include dependencies
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/db/connection.php';

// Create router instance
$router = new Router();

// Get request info
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Static pages
$router->addRoute('GET', '/login', function() {
    include __DIR__ . '/../src/views/login.php';
});

$router->addRoute('GET', '/register', function() {
    include __DIR__ . '/../src/views/register.php';
});

$router->addRoute('GET', '/leagues', function() {
    include __DIR__ . '/leagues.php';
});

$router->addRoute('GET', '/league_scores.php', function() {
    include __DIR__ . '/league_scores.php';
});

// League scores without .php extension for cleaner URLs
$router->addRoute('GET', '/league_scores', function() {
    include __DIR__ . '/league_scores.php';
});

// Backward compatibility routes
$router->addRoute('GET', '/login.php', function() {
    header('Location: /login', true, 301);
    exit;
});

$router->addRoute('GET', '/register.php', function() {
    header('Location: /register', true, 301);
    exit;
});

$router->addRoute('GET', '/leagues.php', function() {
    header('Location: /leagues', true, 301);
    exit;
});

// Placeholder routes for missing pages
$router->addRoute('GET', '/game_page.php', function() {
    echo "<h1>Game Page - Coming Soon!</h1><p><a href='/'>Back to Home</a></p>";
});

$router->addRoute('GET', '/game_page', function() {
    echo "<h1>Game Page - Coming Soon!</h1><p><a href='/'>Back to Home</a></p>";
});

$router->addRoute('GET', '/scores.php', function() {
    echo "<h1>Scores Page - Coming Soon!</h1><p><a href='/'>Back to Home</a></p>";
});

$router->addRoute('GET', '/scores', function() {
    echo "<h1>Scores Page - Coming Soon!</h1><p><a href='/'>Back to Home</a></p>";
});

// Auth routes
$router->addRoute('POST', '/login', function() use ($conn) {
    require_once __DIR__ . '/../src/controllers/AuthController.php';
    $controller = new AuthController($conn);
    $controller->login();
});

$router->addRoute('POST', '/register', function() use ($conn) {
    require_once __DIR__ . '/../src/controllers/AuthController.php';
    $controller = new AuthController($conn);
    $controller->register();
});

$router->addRoute('GET', '/logout', function() use ($conn) {
    require_once __DIR__ . '/../src/controllers/AuthController.php';
    $controller = new AuthController($conn);
    $controller->logout();
});

// League routes
$router->addRoute('POST', '/leagues/create', function() use ($conn) {
    require_once __DIR__ . '/../src/auth/auth_check.php';
    require_once __DIR__ . '/../src/controllers/LeagueController.php';
    $controller = new LeagueController($conn);
    $controller->create();
});

$router->addRoute('POST', '/leagues/join', function() use ($conn) {
    require_once __DIR__ . '/../src/auth/auth_check.php';
    require_once __DIR__ . '/../src/controllers/LeagueController.php';
    $controller = new LeagueController($conn);
    $controller->join();
});

$router->addRoute('POST', '/leagues/leave', function() use ($conn) {
    require_once __DIR__ . '/../src/auth/auth_check.php';
    require_once __DIR__ . '/../src/controllers/LeagueController.php';
    $controller = new LeagueController($conn);
    $controller->leave();
});

// Try to dispatch the request
$dispatched = $router->dispatch($request_uri, $request_method);

// If no route was matched and it's the root path, show the homepage
if (!$dispatched && ($request_uri === '/' || $request_uri === '')) {
    // Close database connection before showing homepage
    if (isset($conn)) {
        $conn->close();
    }
    // Don't exit here, let the homepage render below
} else {
    // Route was handled, close connection and exit
    if (isset($conn)) {
        $conn->close();
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Platform</title>
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
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h1>
                Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!
            </h1>
            <p>What would you like to do?</p>            <div class="nav-links">
                <a href="/game_page">Play Game</a>
                <a href="/scores">View Scores</a>
                <a href="/leagues">View Leagues</a>
                <a href="/logout" class="logout-link">Logout</a>
            </div>
        <?php else: ?>
            <h1>Welcome to the Game Platform!</h1>
            <p>Please log in or register to continue.</p>            <div class="nav-links">
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
