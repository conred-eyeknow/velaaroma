<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "ACTUALIZACIÓN MASIVA DE CATEGORÍAS:\n\n";
    
    // Primero mostrar productos que podrían ser figuras de aroma
    echo "1. PRODUCTOS QUE DEBERÍAN SER FIGURA_AROMA:\n";
    $sql = "SELECT id, name, category FROM list_product 
            WHERE deleted_at IS NULL 
            AND (name LIKE '%muñeca%' 
                OR name LIKE '%perro%' 
                OR name LIKE '%gato%' 
                OR name LIKE '%novios%' 
                OR name LIKE '%angel%' 
                OR name LIKE '%buda%'
                OR name LIKE '%figura%'
                OR category = 'figura_aroma')
            ORDER BY name";
    
    $figuraProducts = MySQLDB::fetchAll($sql);
    
    echo "Productos encontrados: " . count($figuraProducts) . "\n\n";
    
    foreach ($figuraProducts as $product) {
        $currentCategory = $product->category ?: '(vacío)';
        echo "   - ID: {$product->id}, Nombre: {$product->name}, Categoría actual: '{$currentCategory}'\n";
    }
    
    // Contar cuántos necesitan actualización
    $needsUpdate = 0;
    foreach ($figuraProducts as $product) {
        if ($product->category !== 'figura_aroma') {
            $needsUpdate++;
        }
    }
    
    echo "\n2. RESUMEN:\n";
    echo "   - Total productos de figuras encontrados: " . count($figuraProducts) . "\n";
    echo "   - Ya tienen categoría 'figura_aroma': " . (count($figuraProducts) - $needsUpdate) . "\n";
    echo "   - Necesitan actualización: {$needsUpdate}\n";
    
    // Mostrar otros tipos de productos para identificar patrones
    echo "\n3. OTROS TIPOS DE PRODUCTOS:\n";
    $sql = "SELECT DISTINCT name FROM list_product WHERE deleted_at IS NULL AND name NOT LIKE '%muñeca%' AND name NOT LIKE '%perro%' AND name NOT LIKE '%gato%' AND name NOT LIKE '%novios%' ORDER BY name LIMIT 10";
    $others = MySQLDB::fetchAll($sql);
    
    foreach ($others as $product) {
        echo "   - {$product->name}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>