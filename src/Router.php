<?php

class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch($uri, $method) {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $this->matchPath($route['path'], $uri)) {
                $handler = $route['handler'];
                
                if (is_callable($handler)) {
                    $handler();
                    return true;
                } elseif (is_string($handler)) {
                    $this->includeFile($handler);
                    return true;
                }
            }
        }
        
        // No route found - return 404 response
        http_response_code(404);
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #dc3545; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>404 - Page Not Found</h1>
        <p>The requested URL <code>" . htmlspecialchars($uri) . "</code> was not found on this server.</p>
        <p><a href='/'>← Back to Home</a></p>
    </div>
</body>
</html>";
        return false;
    }
    
    private function matchPath($routePath, $uri, &$params = []) {
        // Convert route path to regex for dynamic segments
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        // Match the URI against the pattern
        if (preg_match($pattern, $uri, $matches)) {
            // Extract named parameters
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }
        return false;
    }
    
    private function includeFile($file) {
        if (file_exists($file)) {
            include $file;
        } else {
            http_response_code(404);
            echo "File not found: " . $file;
        }
    }
}
