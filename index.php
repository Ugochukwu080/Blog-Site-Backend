<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/config/app.php';

use App\Controllers\BlogController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\SettingsController;
use App\Middleware\AuthMiddleware;

// Basic Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request URI and Method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip the subfolder base path so routes work whether the app is at the
// webroot (localhost/) or in a subdirectory (localhost/Blog-Site-Backend/)
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($scriptDir && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}
$uri = '/' . ltrim($uri, '/');

/**
 * Helper to match routes with parameters like /api/blogs/{id}
 */
function matchRoute($pattern, $uri) {
    $pattern = preg_replace('/\{[a-zA-Z0-9_-]+\}/', '([a-zA-Z0-9_-]+)', $pattern);
    $pattern = "#^" . $pattern . "$#";
    if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // Remove the full match
        return $matches;
    }
    return false;
}

// ROUTING TABLE
// [Method, Pattern, ControllerClass, Action, isProtected]
$routes = [
    // Public Blog Routes
    ['GET', '/api/blogs', BlogController::class, 'getAllPublished', false],

    // Private Admin Blog Routes — static routes MUST come before /{id} patterns
    ['GET', '/api/blogs/drafts', BlogController::class, 'getAllDrafts', true],
    ['POST', '/api/blogs', BlogController::class, 'create', true],

    // Parameterized Blog Routes
    ['GET', '/api/blogs/{id}', BlogController::class, 'getById', false],
    ['GET', '/api/blogs/slug/{slug}', BlogController::class, 'getBySlug', false],
    ['POST', '/api/blogs/{id}/view', BlogController::class, 'incrementView', false],
    ['PUT', '/api/blogs/{id}', BlogController::class, 'update', true],
    ['PATCH', '/api/blogs/{id}/publish', BlogController::class, 'publish', true],
    ['DELETE', '/api/blogs/{id}', BlogController::class, 'delete', true],

    // Auth Routes
    ['POST', '/api/auth/login', AuthController::class, 'login', false],
    ['PUT', '/api/auth/password', AuthController::class, 'changePassword', true],

    // Book Inventory Routes
    ['GET', '/api/books', BookController::class, 'getAll', false],
    ['GET', '/api/books/{id}', BookController::class, 'getById', false],
    ['GET', '/api/books/slug/{slug}', BookController::class, 'getBySlug', false],
    ['POST', '/api/books', BookController::class, 'create', true],
    ['POST', '/api/books/{id}', BookController::class, 'update', true],
    ['DELETE', '/api/books/{id}', BookController::class, 'delete', true],

    // Settings Routes
    ['GET', '/api/settings', SettingsController::class, 'get', false],
    ['PUT', '/api/settings', SettingsController::class, 'update', true]
];

$matched = false;

foreach ($routes as $route) {
    list($rMethod, $rPattern, $rController, $rAction, $rProtected) = $route;

    if ($method === $rMethod) {
        $params = matchRoute($rPattern, $uri);
        if ($params !== false) {
            $matched = true;
            
            // Check Protection
            if ($rProtected) {
                AuthMiddleware::authenticate();
            }

            // Call Controller Action
            $controller = new $rController();
            call_user_func_array([$controller, $rAction], $params);
            break;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Route not found"]);
}
