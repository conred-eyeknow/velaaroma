<?php
/**
 * API Endpoint: /api/cart/products
 * Maneja operaciones del carrito de compras
 */

require_once '../mysql_config.php';
require_once '../controllers/mysql_cart.php';
require_once '../utils.php';

// Configurar headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    
    // Determinar parámetro adicional (como 'sell')
    $param = null;
    if (count($segments) > 3) {
        $param = $segments[3]; // /api/cart/products/sell
    }
    
    // Instanciar controlador
    $controller = new MySQLCartController();
    
    // Manejar la solicitud
    $controller->handleRequest('products', $method, $param);
    
} catch (Exception $e) {
    http_response_code(500);
    sendJsonResponse(['error' => $e->getMessage()]);
}
?>