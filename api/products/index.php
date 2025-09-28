<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../mysql_config.php';
    
    // Obtener parámetros de consulta
    $category = $_GET['category'] ?? null;
    $limit = intval($_GET['limit'] ?? 100);
    $offset = intval($_GET['offset'] ?? 0);
    
    // Construir consulta base - CON filtro de deleted_at
    $sql = "SELECT id, name, description, mayoreo, menudeo, largo, alto, ancho, category, url, created_at 
            FROM list_product 
            WHERE (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
    
    $params = [];
    $whereAdded = true;
    
    // Agregar filtro por categoría si se especifica
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    // Agregar ordenación y límites
    $sql .= " ORDER BY created_at DESC, name ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    // Ejecutar consulta usando MySQLDB
    $products = MySQLDB::fetchAll($sql, $params);
    
    // Contar total de productos (solo los no eliminados)
    $countSql = "SELECT COUNT(*) as total FROM list_product WHERE (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
    $countParams = [];
    if ($category) {
        $countSql .= " AND category = ?";
        $countParams[] = $category;
    }
    
    $totalResult = MySQLDB::fetchOne($countSql, $countParams);
    $totalCount = $totalResult->total;
    
    // Formatear productos - convertir objetos a arrays para compatibilidad
    $formattedProducts = [];
    foreach ($products as $product) {
        $productArray = (array) $product;
        
        // Validar y corregir URL de imagen
        $productArray['url'] = validateAndFixImageUrl($productArray['url'], $productArray['category']);
        
        // Convertir valores numéricos
        $productArray['mayoreo'] = floatval($productArray['mayoreo']);
        $productArray['menudeo'] = floatval($productArray['menudeo']);
        $productArray['largo'] = floatval($productArray['largo']);
        $productArray['alto'] = floatval($productArray['alto']);
        $productArray['ancho'] = floatval($productArray['ancho']);
        
        $formattedProducts[] = $productArray;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $formattedProducts,
        'pagination' => [
            'total' => intval($totalCount),
            'count' => count($formattedProducts),
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

/**
 * Valida y corrige la URL de imagen del producto
 */
function validateAndFixImageUrl($url, $category) {
    // Limpiar la URL de espacios y saltos de línea
    $url = trim($url);
    
    // Si ya es una URL completa (http/https), mantenerla
    if ($url && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
        return $url;
    }
    
    // Si está vacía, usar imagen por defecto
    if (empty($url)) {
        return '/images/default_product.jpg';
    }
    
    // Asegurar que tenga el prefijo /images/
    if (!str_starts_with($url, '/images/')) {
        if (str_starts_with($url, '/')) {
            $url = '/images' . $url;
        } else {
            $url = '/images/' . $url;
        }
    }
    
    // Verificar si el archivo existe físicamente
    $imagePath = __DIR__ . '/../../' . ltrim($url, '/');
    
    if (file_exists($imagePath)) {
        return $url;
    }
    
    // Si no existe, intentar variaciones comunes
    $filename = basename($url);
    $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    $possibleExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    foreach ($possibleExtensions as $ext) {
        $testPath = __DIR__ . '/../../images/' . $nameWithoutExt . '.' . $ext;
        if (file_exists($testPath)) {
            return '/images/' . $nameWithoutExt . '.' . $ext;
        }
    }
    
    // Intentar con "figuras" en lugar de "figura" si es figura_aroma
    if ($category === 'figura_aroma' && strpos($filename, 'figura_aroma') !== false) {
        $fixedFilename = str_replace('figura_aroma', 'figuras_aroma', $filename);
        $testPath = __DIR__ . '/../../images/' . $fixedFilename;
        if (file_exists($testPath)) {
            return '/images/' . $fixedFilename;
        }
        
        // También probar con diferentes extensiones
        $nameWithoutExt = pathinfo($fixedFilename, PATHINFO_FILENAME);
        foreach ($possibleExtensions as $ext) {
            $testPath = __DIR__ . '/../../images/' . $nameWithoutExt . '.' . $ext;
            if (file_exists($testPath)) {
                return '/images/' . $nameWithoutExt . '.' . $ext;
            }
        }
    }
    
    // Si no se encuentra nada, usar imagen por defecto
    return '/images/default_product.jpg';
}
?>