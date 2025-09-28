<?php
require_once __DIR__ . '/mysql_config.php';

try {
    // Buscar todas las tablas que contengan 'cart' o similar
    $sql = "SHOW TABLES LIKE '%cart%'";
    $tables = MySQLDB::fetchAll($sql);
    
    echo "Tablas relacionadas con carrito:\n";
    foreach ($tables as $table) {
        foreach ($table as $tableName) {
            echo "- $tableName\n";
        }
    }
    
    // Buscar tablas que podrían ser del carrito
    $sql = "SHOW TABLES";
    $allTables = MySQLDB::fetchAll($sql);
    
    echo "\nTodas las tablas disponibles:\n";
    foreach ($allTables as $table) {
        foreach ($table as $tableName) {
            echo "- $tableName\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>