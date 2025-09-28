<?php
require_once __DIR__ . '/mysql_config.php';

try {
    // Verificar estructura de la tabla va_cart
    $sql = "DESCRIBE va_cart";
    $columns = MySQLDB::fetchAll($sql);
    
    echo "Estructura de la tabla va_cart:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    // Verificar si hay datos de prueba
    $sql = "SELECT * FROM va_cart LIMIT 3";
    $sample = MySQLDB::fetchAll($sql);
    
    echo "\nDatos de muestra:\n";
    if ($sample) {
        print_r($sample);
    } else {
        echo "No hay datos en la tabla\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>