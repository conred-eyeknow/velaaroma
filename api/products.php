<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/mysql_config.php';
    
    // Obtener parámetros de consulta
    $category = $_GET['category'] ?? null;
    $limit = intval($_GET['limit'] ?? 100);
    $offset = intval($_GET['offset'] ?? 0);
    
    // Construir consulta base
    $sql = "SELECT id, name, description, mayoreo, menudeo, largo, alto, ancho, category, url, created_at 
            FROM list_product 
            WHERE deleted_at IS NULL";
    
    $params = [];
    
    // Agregar filtro por categoría si se especifica
    if ($category) {
        $sql .= " AND category = :category";
        $params['category'] = $category;
    }
    
    // Agregar ordenación y límites
    $sql .= " ORDER BY created_at DESC, name ASC LIMIT :limit OFFSET :offset";
    
    // Preparar y ejecutar consulta
    $stmt = $pdo->prepare($sql);
    
    // Bind parámetros
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de productos
    $countSql = "SELECT COUNT(*) as total FROM list_product WHERE deleted_at IS NULL";
    if ($category) {
        $countSql .= " AND category = :category";
    }
    
    $countStmt = $pdo->prepare($countSql);
    if ($category) {
        $countStmt->bindValue(':category', $category);
    }
    $countStmt->execute();
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Formatear URLs de imágenes
    foreach ($products as &$product) {
        // Asegurar que la URL sea completa
        if ($product['url'] && !str_starts_with($product['url'], 'http')) {
            if (!str_starts_with($product['url'], '/')) {
                $product['url'] = '/images/' . $product['url'];
            }
        }
        
        // Convertir valores numéricos
        $product['mayoreo'] = floatval($product['mayoreo']);
        $product['menudeo'] = floatval($product['menudeo']);
        $product['largo'] = floatval($product['largo']);
        $product['alto'] = floatval($product['alto']);
        $product['ancho'] = floatval($product['ancho']);
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'total' => intval($totalCount),
            'count' => count($products),
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $totalCount
        ],
        'filters' => [
            'category' => $category
        ]
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