<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "ANÁLISIS COMPLETO DE PRODUCTOS:\n\n";
    
    // 1. Total de productos
    $sql = "SELECT COUNT(*) as total FROM list_product";
    $result = MySQLDB::fetchOne($sql);
    echo "1. Total de productos en BD: " . $result->total . "\n";
    
    // 2. Productos con deleted_at NULL
    $sql = "SELECT COUNT(*) as total FROM list_product WHERE deleted_at IS NULL";
    $result = MySQLDB::fetchOne($sql);
    echo "2. Productos activos (deleted_at IS NULL): " . $result->total . "\n";
    
    // 3. Productos con deleted_at NO NULL
    $sql = "SELECT COUNT(*) as total FROM list_product WHERE deleted_at IS NOT NULL";
    $result = MySQLDB::fetchOne($sql);
    echo "3. Productos eliminados (deleted_at IS NOT NULL): " . $result->total . "\n";
    
    // 4. Verificar qué hay en deleted_at
    $sql = "SELECT deleted_at, COUNT(*) as count FROM list_product GROUP BY deleted_at ORDER BY count DESC LIMIT 10";
    $results = MySQLDB::fetchAll($sql);
    echo "\n4. Valores de deleted_at:\n";
    foreach ($results as $row) {
        $deleted = $row->deleted_at ?: 'NULL';
        echo "   - {$deleted}: {$row->count} productos\n";
    }
    
    // 5. Mostrar algunos productos eliminados
    $sql = "SELECT id, name, deleted_at FROM list_product WHERE deleted_at IS NOT NULL LIMIT 5";
    $results = MySQLDB::fetchAll($sql);
    echo "\n5. Ejemplos de productos eliminados:\n";
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Eliminado: {$row->deleted_at}\n";
    }
    
    // 6. Mostrar productos activos
    $sql = "SELECT id, name, category, created_at FROM list_product WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5";
    $results = MySQLDB::fetchAll($sql);
    echo "\n6. Productos activos recientes:\n";
    foreach ($results as $row) {
        echo "   - ID: {$row->id}, Nombre: {$row->name}, Categoría: {$row->category}, Creado: {$row->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>