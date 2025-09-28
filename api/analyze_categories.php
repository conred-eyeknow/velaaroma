<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "ANÁLISIS DE PRODUCTOS POR CATEGORÍA:\n\n";
    
    // 1. Ver todas las categorías disponibles
    echo "1. TODAS LAS CATEGORÍAS:\n";
    $sql = "SELECT category, COUNT(*) as count FROM list_product WHERE deleted_at IS NULL GROUP BY category ORDER BY count DESC";
    $results = MySQLDB::fetchAll($sql);
    foreach ($results as $row) {
        $cat = $row->category ?: '(vacío)';
        echo "   - '{$cat}': {$row->count} productos\n";
    }
    
    // 2. Buscar productos que contengan "figura"
    echo "\n2. PRODUCTOS QUE CONTIENEN 'figura' EN CATEGORÍA:\n";
    $sql = "SELECT id, name, category FROM list_product WHERE deleted_at IS NULL AND category LIKE '%figura%'";
    $results = MySQLDB::fetchAll($sql);
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Categoría: '{$row->category}'\n";
    }
    
    // 3. Buscar productos que contengan "aroma"
    echo "\n3. PRODUCTOS QUE CONTIENEN 'aroma' EN CATEGORÍA:\n";
    $sql = "SELECT id, name, category FROM list_product WHERE deleted_at IS NULL AND category LIKE '%aroma%'";
    $results = MySQLDB::fetchAll($sql);
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Categoría: '{$row->category}'\n";
    }
    
    // 4. Buscar productos exactamente con "figura_aroma"
    echo "\n4. PRODUCTOS CON CATEGORÍA EXACTA 'figura_aroma':\n";
    $sql = "SELECT id, name, category FROM list_product WHERE deleted_at IS NULL AND category = 'figura_aroma'";
    $results = MySQLDB::fetchAll($sql);
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Categoría: '{$row->category}'\n";
    }
    
    // 5. Ver algunos productos por nombre para entender las categorías
    echo "\n5. PRODUCTOS QUE PODRÍAN SER FIGURAS (por nombre):\n";
    $sql = "SELECT id, name, category FROM list_product WHERE deleted_at IS NULL AND name LIKE '%muñeca%' OR name LIKE '%perro%' OR name LIKE '%novios%' LIMIT 10";
    $results = MySQLDB::fetchAll($sql);
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Categoría: '{$row->category}'\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>