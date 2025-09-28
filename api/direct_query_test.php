<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "CONSULTA DIRECTA A LA BASE DE DATOS:\n\n";
    
    // 1. Contar productos con categoria figura_aroma
    echo "1. CONTEO DE PRODUCTOS CON CATEGORIA 'figura_aroma':\n";
    $sql = "SELECT COUNT(*) as total FROM list_product WHERE category = 'figura_aroma' AND deleted_at IS NULL";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total: {$result->total} productos\n\n";
    
    // 2. Mostrar los primeros 10 productos
    echo "2. PRIMEROS 10 PRODUCTOS CON CATEGORIA 'figura_aroma':\n";
    $sql = "SELECT id, name, category, mayoreo, menudeo FROM list_product WHERE category = 'figura_aroma' AND deleted_at IS NULL ORDER BY name LIMIT 10";
    $products = MySQLDB::fetchAll($sql);
    
    foreach ($products as $product) {
        echo "   - ID: {$product->id}, Nombre: {$product->name}, Mayoreo: \${$product->mayoreo}, Menudeo: \${$product->menudeo}\n";
    }
    
    // 3. Probar la misma consulta que usa el endpoint
    echo "\n3. SIMULANDO LA CONSULTA DEL ENDPOINT:\n";
    $category = 'figura_aroma';
    $limit = 100;
    $offset = 0;
    
    $sql = "SELECT id, name, description, mayoreo, menudeo, largo, alto, ancho, category, url, created_at 
            FROM list_product 
            WHERE deleted_at IS NULL AND category = ?
            ORDER BY created_at DESC, name ASC LIMIT ? OFFSET ?";
    
    $params = [$category, $limit, $offset];
    echo "   SQL: {$sql}\n";
    echo "   Parámetros: " . json_encode($params) . "\n";
    
    $products = MySQLDB::fetchAll($sql, $params);
    echo "   Resultados encontrados: " . count($products) . "\n\n";
    
    // 4. Mostrar algunos resultados
    echo "4. PRIMEROS 5 RESULTADOS DE LA CONSULTA DEL ENDPOINT:\n";
    $count = 0;
    foreach ($products as $product) {
        if ($count < 5) {
            echo "   - ID: {$product->id}, Nombre: {$product->name}, Categoría: {$product->category}\n";
            $count++;
        }
    }
    
    // 5. Verificar conteo total para paginación
    echo "\n5. CONTEO PARA PAGINACIÓN:\n";
    $countSql = "SELECT COUNT(*) as total FROM list_product WHERE deleted_at IS NULL AND category = ?";
    $countParams = [$category];
    $totalResult = MySQLDB::fetchOne($countSql, $countParams);
    echo "   Total para paginación: {$totalResult->total}\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>