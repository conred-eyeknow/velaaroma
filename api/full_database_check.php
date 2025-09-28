<?php
require_once __DIR__ . '/mysql_config.php';

echo "=== VERIFICACIÓN COMPLETA DE BASE DE DATOS ===\n\n";

try {
    // 1. Verificar conexión
    echo "1. CONEXIÓN A BASE DE DATOS:\n";
    $sql = "SELECT DATABASE() as current_db";
    $result = MySQLDB::fetchOne($sql);
    echo "   ✅ Conectado a: " . $result->current_db . "\n\n";
    
    // 2. Verificar productos
    echo "2. PRODUCTOS (list_product):\n";
    $sql = "SELECT COUNT(*) as total FROM list_product";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total productos: " . $result->total . "\n";
    
    $sql = "SELECT COUNT(*) as active FROM list_product WHERE status = 'active'";
    $result = MySQLDB::fetchOne($sql);
    echo "   Productos activos: " . $result->active . "\n";
    
    // Mostrar algunos productos
    $sql = "SELECT id, name, categoria, status, mayoreo, menudeo FROM list_product WHERE status = 'active' LIMIT 5";
    $products = MySQLDB::fetchAll($sql);
    echo "   Primeros 5 productos activos:\n";
    foreach ($products as $product) {
        echo "   - ID: {$product->id}, Nombre: {$product->name}, Categoría: {$product->categoria}, Mayoreo: \${$product->mayoreo}, Menudeo: \${$product->menudeo}\n";
    }
    echo "\n";
    
    // 3. Verificar carrito
    echo "3. CARRITO (cart):\n";
    $sql = "SELECT COUNT(*) as total FROM cart";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total items en carrito: " . $result->total . "\n";
    
    $sql = "SELECT COUNT(*) as active FROM cart WHERE status = 'in_progress' AND deleted_at IS NULL";
    $result = MySQLDB::fetchOne($sql);
    echo "   Items activos en carrito: " . $result->active . "\n";
    
    // Mostrar algunos items del carrito
    $sql = "SELECT username, product_id, aroma, color, cantidad, valor, status FROM cart WHERE deleted_at IS NULL LIMIT 5";
    $cartItems = MySQLDB::fetchAll($sql);
    echo "   Primeros 5 items del carrito:\n";
    foreach ($cartItems as $item) {
        echo "   - Usuario: {$item->username}, Producto: {$item->product_id}, Status: {$item->status}, Cantidad: {$item->cantidad}, Valor: \${$item->valor}\n";
    }
    echo "\n";
    
    // 4. Verificar usuarios
    echo "4. USUARIOS (users):\n";
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = MySQLDB::fetchOne($sql);
    echo "   Total usuarios: " . $result->total . "\n";
    
    // 5. Test específico del endpoint del carrito
    echo "5. TEST ESPECÍFICO DEL CARRITO:\n";
    $sql = "SELECT * FROM cart WHERE username = 'usuario852_53' AND status = 'in_progress' AND deleted_at IS NULL";
    $userCart = MySQLDB::fetchAll($sql);
    echo "   Items para usuario852_53: " . count($userCart) . "\n";
    
    if (count($userCart) > 0) {
        foreach ($userCart as $item) {
            echo "   - Producto: {$item->product_id}, Aroma: {$item->aroma}, Color: {$item->color}, Cantidad: {$item->cantidad}\n";
        }
    } else {
        echo "   No hay items activos para este usuario\n";
    }
    echo "\n";
    
    // 6. Verificar categorías de productos
    echo "6. CATEGORÍAS DE PRODUCTOS:\n";
    $sql = "SELECT categoria, COUNT(*) as count FROM list_product WHERE status = 'active' GROUP BY categoria";
    $categories = MySQLDB::fetchAll($sql);
    foreach ($categories as $cat) {
        echo "   - {$cat->categoria}: {$cat->count} productos\n";
    }
    echo "\n";
    
    echo "✅ VERIFICACIÓN COMPLETA - TODO CONECTADO A BASE DE DATOS REAL\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>