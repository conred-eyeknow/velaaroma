<?php
/**
 * API Endpoint: /api/cart/products/index.php
 * Maneja operaciones del carrito de compras
 */

require_once '../../mysql_config.php';
require_once '../../controllers/mysql_cart.php';
require_once '../../utils.php';

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
    
    // Instanciar controlador
    $controller = new MySQLCartController();
    
    // Manejar la solicitud - siempre es 'products' para este endpoint
    $controller->handleRequest('products', $method, null);
    
} catch (Exception $e) {
    http_response_code(500);
    sendJsonResponse(['error' => $e->getMessage()]);
}
?>