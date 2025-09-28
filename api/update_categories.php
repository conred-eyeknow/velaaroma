<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "ACTUALIZACIÓN DE CATEGORÍAS BASADA EN LA ESTRUCTURA ORIGINAL:\n\n";
    
    // Basándome en los archivos de la estructura original del proyecto:
    // velas-figuras/ -> figura_aroma
    // velas-vidrio/ -> velas_vidrio  
    // velas-yeso/ -> velas_yeso
    // velas-dia-de-muertos/ -> dia_de_muertos
    
    echo "1. ACTUALIZANDO PRODUCTOS QUE DEBERÍAN SER 'figura_aroma':\n";
    
    // Productos que claramente son figuras de aroma basándome en los nombres que vi
    $figuraProducts = [
        'Muñeca' => 'figura_aroma',
        'Perro grande' => 'figura_aroma', 
        'Perro pequeño' => 'figura_aroma',
        'Novios eternos' => 'figura_aroma',
        'Perro rosas' => 'figura_aroma',
        'Novios' => 'figura_aroma'
    ];
    
    $updated = 0;
    
    foreach ($figuraProducts as $productName => $category) {
        // Buscar el producto por nombre
        $sql = "SELECT id, name, category FROM list_product WHERE name = ? AND deleted_at IS NULL";
        $products = MySQLDB::fetchAll($sql, [$productName]);
        
        foreach ($products as $product) {
            if ($product->category !== $category) {
                // Actualizar la categoría
                $updateSql = "UPDATE list_product SET category = ? WHERE id = ?";
                MySQLDB::execute($updateSql, [$category, $product->id]);
                
                echo "   ✅ Actualizado: '{$product->name}' -> categoria '{$category}'\n";
                $updated++;
            } else {
                echo "   ⚠️  Ya correcto: '{$product->name}' -> categoria '{$category}'\n";
            }
        }
    }
    
    echo "\n2. VERIFICANDO RESULTADO:\n";
    $sql = "SELECT COUNT(*) as count FROM list_product WHERE category = 'figura_aroma' AND deleted_at IS NULL";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total productos con categoria 'figura_aroma': {$result->count}\n";
    
    // Mostrar todos los productos con esta categoría
    $sql = "SELECT id, name, category FROM list_product WHERE category = 'figura_aroma' AND deleted_at IS NULL ORDER BY name";
    $products = MySQLDB::fetchAll($sql);
    
    echo "\n3. PRODUCTOS CON CATEGORÍA 'figura_aroma':\n";
    foreach ($products as $product) {
        echo "   - ID: {$product->id}, Nombre: {$product->name}\n";
    }
    
    echo "\n4. PRODUCTOS ACTUALIZADOS: {$updated}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>