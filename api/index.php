<?php
/**
 * API Router - Punto de entrada único
 * Maneja todas las rutas de la API interna
 */

// Habilitar modo debug (opcional)
define('DEBUG_MODE', true);

// Configurar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

// Configurar headers de seguridad y CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'mysql_config.php';
require_once 'utils.php';

// Rate limiting básico
$clientIP = getClientIP();
if (!rateLimitCheck($clientIP)) {
    sendJsonResponse(['error' => 'Demasiadas solicitudes'], 429);
}

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Extraer la ruta después de /api/
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$path = trim($path, '/');

// Dividir la ruta en segmentos
$segments = explode('/', $path);

// Determinar el controlador y acción
$controller = $segments[0] ?? '';
$action = $segments[1] ?? '';
$param = $segments[2] ?? '';

try {
    switch ($controller) {
        case 'users':
            require_once 'controllers/mysql_users.php';
            $userController = new MySQLUsersController();
            $userController->handleRequest($action, $method, $param);
            break;
            
        case 'products':
            require_once 'controllers/mysql_products.php';
            $productController = new MySQLProductsController();
            $productController->handleRequest($action, $method, $param);
            break;
            
        case 'cart':
            require_once 'controllers/mysql_cart.php';
            $cartController = new MySQLCartController();
            $cartController->handleRequest($action, $method, $param);
            break;
            
        default:
            sendJsonResponse(['error' => 'Ruta no encontrada'], 404);
    }
    
} catch (Exception $e) {
    sendJsonResponse(['error' => $e->getMessage()], 500);
}
?>