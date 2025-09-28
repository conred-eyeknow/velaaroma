<?php
/**
 * API Endpoint: Eliminar Producto
 * Marca un producto como eliminado (soft delete)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once __DIR__ . '/../mysql_config.php';
    
    // Verificar que es una petición POST o DELETE
    if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos de la petición
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Validar que se proporcionó el ID
    $productId = $data['id'] ?? null;
    
    if (empty($productId)) {
        throw new Exception('ID del producto es requerido');
    }
    
    // Verificar que el producto existe y realizar soft delete
    $stmt = $pdo->prepare("SELECT id, name FROM list_product WHERE id = ? AND (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Producto no encontrado');
    }
    
    // Realizar soft delete (marcar como eliminado)
    $stmt = $pdo->prepare("UPDATE list_product SET deleted_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$productId]);
    
    if (!$result) {
        throw new Exception('Error al eliminar el producto');
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado exitosamente',
        'product_id' => $productId,
        'product_name' => $product['name']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'file' => __FILE__,
            'line' => __LINE__
        ]
    ]);
}
?>