<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "VERIFICACIÓN DE CONEXIÓN DE BASE DE DATOS:\n\n";
    
    // 1. Información de la conexión actual
    echo "1. INFORMACIÓN DE LA CONEXIÓN:\n";
    $sql = "SELECT DATABASE() as current_db";
    $result = MySQLDB::fetchOne($sql);
    echo "   Base de datos: {$result->current_db}\n\n";
    
    // 2. Contar TODOS los productos sin filtros
    echo "2. CONTEO TOTAL SIN FILTROS:\n";
    $sql = "SELECT COUNT(*) as total FROM list_product";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total productos en tabla: {$result->total}\n";
    
    // 3. Conteo por deleted_at
    echo "\n3. ANÁLISIS DE deleted_at:\n";
    $sql = "SELECT 
                CASE 
                    WHEN deleted_at IS NULL THEN 'NULL (activos)'
                    ELSE 'NO NULL (eliminados)'
                END as estado,
                COUNT(*) as count
            FROM list_product 
            GROUP BY deleted_at IS NULL";
    $results = MySQLDB::fetchAll($sql);
    
    foreach ($results as $row) {
        echo "   - {$row->estado}: {$row->count} productos\n";
    }
    
    // 4. Buscar productos específicos que aparecen en la imagen
    echo "\n4. BÚSQUEDA DE PRODUCTOS ESPECÍFICOS DE LA IMAGEN:\n";
    $productNames = ['Oso', 'Tigre', 'Cuerda de lana', 'Budda', 'Arco minimalista'];
    
    foreach ($productNames as $name) {
        $sql = "SELECT id, name, category, deleted_at FROM list_product WHERE name LIKE ?";
        $results = MySQLDB::fetchAll($sql, ["%{$name}%"]);
        
        if ($results) {
            foreach ($results as $product) {
                $deletedStatus = $product->deleted_at ? "ELIMINADO ({$product->deleted_at})" : "ACTIVO";
                echo "   - Encontrado '{$name}': ID {$product->id}, Categoría: '{$product->category}', Estado: {$deletedStatus}\n";
            }
        } else {
            echo "   - '{$name}': NO ENCONTRADO\n";
        }
    }
    
    // 5. Mostrar todos los productos con category figura_aroma sin importar deleted_at
    echo "\n5. TODOS LOS PRODUCTOS CON CATEGORIA 'figura_aroma' (INCLUIDOS ELIMINADOS):\n";
    $sql = "SELECT id, name, category, deleted_at FROM list_product WHERE category = 'figura_aroma' ORDER BY name LIMIT 10";
    $results = MySQLDB::fetchAll($sql);
    
    echo "   Encontrados: " . count($results) . " productos\n";
    foreach ($results as $product) {
        $deletedStatus = $product->deleted_at ? "ELIMINADO" : "ACTIVO";
        echo "   - ID: {$product->id}, Nombre: {$product->name}, Estado: {$deletedStatus}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>