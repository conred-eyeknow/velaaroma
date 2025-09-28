<?php
/**
 * Pruebas de API - Para verificar que todo funciona
 */

require_once 'api/config.php';

echo "<h1>🧪 Pruebas de API - Vela Aroma</h1>";

// Función auxiliar para hacer peticiones
function makeRequest($url, $method = 'GET', $data = null) {
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'content' => $data ? json_encode($data) : null
        ]
    ]);
    
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

$baseUrl = 'http://localhost:8000/api/';

echo "<h2>📋 Pruebas Disponibles</h2>";

// Prueba 1: Listar productos
echo "<h3>1. 🕯️ Listar Productos</h3>";
try {
    $products = makeRequest($baseUrl . 'products');
    if ($products && isset($products['products'])) {
        echo "✅ OK - " . count($products['products']) . " productos encontrados<br>";
        foreach ($products['products'] as $product) {
            echo "• {$product['name']} - Categoría: {$product['category']} - Precio: \${$product['menudeo']}<br>";
        }
    } else {
        echo "❌ Error obteniendo productos<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Prueba 2: Productos por categoría
echo "<h3>2. 📂 Productos por Categoría</h3>";
try {
    $figuras = makeRequest($baseUrl . 'products/category?category=figura_aroma');
    if ($figuras && isset($figuras['products'])) {
        echo "✅ OK - " . count($figuras['products']) . " velas con figuras encontradas<br>";
    } else {
        echo "❌ Error obteniendo productos por categoría<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Prueba 3: Validar usuario admin
echo "<h3>3. 👤 Validar Usuario Admin</h3>";
try {
    $loginData = [
        'username' => 'admin',
        'password' => 'admin123'
    ];
    
    $response = makeRequest($baseUrl . 'users/validate', 'POST', $loginData);
    if ($response && isset($response['info']) && $response['info'][0]['id'] > 0) {
        echo "✅ OK - Usuario admin validado correctamente<br>";
        echo "• ID: {$response['info'][0]['id']}<br>";
        echo "• Nombre: {$response['info'][0]['name']}<br>";
    } else {
        echo "❌ Error validando usuario admin<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Prueba 4: Carrito vacío
echo "<h3>4. 🛒 Carrito Vacío</h3>";
try {
    $cart = makeRequest($baseUrl . 'cart/products?username=test_user&status=in_progress');
    if ($cart && isset($cart['products'])) {
        echo "✅ OK - Carrito vacío: {$cart['products']} productos<br>";
    } else {
        echo "❌ Error obteniendo carrito<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>📊 Estado de Archivos de Datos</h2>";

$files = [
    'users.json' => USERS_FILE,
    'products.json' => PRODUCTS_FILE,
    'cart.json' => CART_FILE,
    'sessions.json' => SESSIONS_FILE
];

foreach ($files as $name => $file) {
    if (file_exists($file)) {
        $data = JsonDB::read($file);
        $count = count($data);
        $size = filesize($file);
        echo "✅ $name - $count registros - $size bytes<br>";
    } else {
        echo "❌ $name - No existe<br>";
    }
}

echo "<br><p><a href='./'>🏠 Volver al sitio</a> | <a href='setup.php'>⚙️ Ejecutar setup</a></p>";
?>