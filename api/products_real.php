<?php
/**
 * ENDPOINT SIMPLE DE PRODUCTOS
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'mysql_config_real.php';

try {
    // Conectar a la base de datos
    $connected = MySQLDB::isConnected();
    
    if (!$connected) {
        echo json_encode(['error' => 'Sin conexión a BD', 'products' => []]);
        exit;
    }
    
    // Obtener productos
    $sql = "SELECT * FROM list_product WHERE (deleted_at IS NULL OR deleted_at = '') LIMIT 50";
    $products = MySQLDB::fetchAll($sql);
    
    // Convertir a array para el frontend
    $result = [];
    foreach ($products as $product) {
        $result[] = [
            'id' => $product->id,
            'nombre' => $product->name,
            'descripcion' => $product->description ?? '',
            'categoria' => $product->category,
            'precio' => $product->menudeo,
            'precio_mayoreo' => $product->mayoreo,
            'imagen' => str_replace('https://velaaroma.com', '', $product->url),
            'largo' => $product->largo ?? '0',
            'alto' => $product->alto ?? '0',
            'ancho' => $product->ancho ?? '0',
            'peso' => $product->peso ?? '0',
            'created_at' => $product->created_at ?? '',
            'data_source' => 'REAL_DATABASE'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'total' => count($result),
        'data_source' => 'REAL_DATABASE',
        'products' => $result
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'products' => [],
        'data_source' => 'ERROR'
    ]);
}
?>