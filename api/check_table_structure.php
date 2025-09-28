<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "ESTRUCTURA DE TABLAS:\n\n";
    
    // Verificar estructura de list_product
    echo "=== TABLA: list_product ===\n";
    $sql = "DESCRIBE list_product";
    $columns = MySQLDB::fetchAll($sql);
    
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    // Verificar algunos datos
    echo "\nDatos de muestra:\n";
    $sql = "SELECT * FROM list_product LIMIT 3";
    $sample = MySQLDB::fetchAll($sql);
    
    if ($sample) {
        foreach ($sample as $product) {
            echo "ID: {$product->id}, Nombre: {$product->name}, Categoría: {$product->categoria}\n";
        }
    }
    
    echo "\n=== TABLA: cart ===\n";
    $sql = "DESCRIBE cart";
    $columns = MySQLDB::fetchAll($sql);
    
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>