<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "=== TABLA: cart ===\n";
    $sql = "DESCRIBE cart";
    $columns = MySQLDB::fetchAll($sql);
    
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    // Verificar datos de muestra
    $sql = "SELECT * FROM cart LIMIT 3";
    $sample = MySQLDB::fetchAll($sql);
    
    echo "\nDatos de muestra en 'cart':\n";
    if ($sample) {
        print_r($sample);
    } else {
        echo "No hay datos en la tabla 'cart'\n";
    }
    
    echo "\n=== TABLA: va_cart ===\n";
    $sql = "DESCRIBE va_cart";
    $columns = MySQLDB::fetchAll($sql);
    
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    // Verificar datos de muestra
    $sql = "SELECT * FROM va_cart LIMIT 3";
    $sample = MySQLDB::fetchAll($sql);
    
    echo "\nDatos de muestra en 'va_cart':\n";
    if ($sample) {
        print_r($sample);
    } else {
        echo "No hay datos en la tabla 'va_cart'\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>