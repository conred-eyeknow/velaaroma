<?php
/**
 * Script de inicialización y migración de datos
 * Ejecutar una sola vez para configurar el sistema
 */

require_once 'api/config.php';

echo "<h1>🚀 Inicializando Sistema Vela Aroma</h1>";

// Crear directorios necesarios
$directories = [
    DATA_DIR,
    DATA_DIR . 'backups/',
    DATA_DIR . 'logs/'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Directorio creado: $dir<br>";
    } else {
        echo "📁 Directorio existe: $dir<br>";
    }
}

// Inicializar archivos de datos
$dataFiles = [
    USERS_FILE => [],
    PRODUCTS_FILE => [],
    CART_FILE => [],
    SESSIONS_FILE => [],
    DATA_DIR . 'orders.json' => [],
    DATA_DIR . 'sent_emails.json' => []
];

foreach ($dataFiles as $file => $defaultData) {
    if (!file_exists($file)) {
        JsonDB::write($file, $defaultData);
        echo "✅ Archivo creado: " . basename($file) . "<br>";
    } else {
        echo "📄 Archivo existe: " . basename($file) . "<br>";
    }
}

// Crear usuario administrador por defecto
$users = JsonDB::read(USERS_FILE);
$adminExists = JsonDB::findByField($users, 'username', 'admin');

if (!$adminExists) {
    $admin = [
        'id' => 'admin_' . time(),
        'name' => 'Administrador',
        'first_last_name' => 'Sistema',
        'second_last_name' => '',
        'email' => 'admin@velaaroma.com',
        'username' => 'admin',
        'password' => hashPassword('admin123'),
        'address' => '',
        'zipcode' => '',
        'telephone' => '',
        'virtual_address_is_validated' => '1',
        'validation_code' => null,
        'role' => 'admin',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $users[] = $admin;
    JsonDB::write(USERS_FILE, $users);
    echo "✅ Usuario administrador creado<br>";
    echo "📝 <strong>Usuario:</strong> admin<br>";
    echo "📝 <strong>Contraseña:</strong> admin123<br>";
} else {
    echo "👤 Usuario administrador ya existe<br>";
}

// Inicializar productos por defecto
$products = JsonDB::read(PRODUCTS_FILE);
if (empty($products)) {
    // Llamar al controlador para inicializar productos
    require_once 'api/controllers/products.php';
    $productController = new ProductsController();
    $productController->createProduct(); // Esto activará la inicialización
    echo "✅ Productos por defecto creados<br>";
} else {
    echo "🕯️ Productos ya existen (" . count($products) . " productos)<br>";
}

// Crear archivo de configuración local
$configContent = '<?php
// Configuración local - generada automáticamente
define("SITE_NAME", "Vela Aroma");
define("SITE_URL", "http://localhost:8000");
define("API_URL", "./api/");
define("DEBUG_MODE", true);
define("EMAIL_ENABLED", false); // Cambiar a true para envío real de emails
?>';

file_put_contents('config_local.php', $configContent);
echo "✅ Archivo de configuración local creado<br>";

echo "<br><h2>🎉 Inicialización Completada</h2>";
echo "<p><strong>Todo está listo para usar!</strong></p>";
echo "<p><a href='./'>🏠 Ir al sitio principal</a></p>";
echo "<p><a href='admin/'>⚙️ Panel de administración</a></p>";
echo "<p><a href='api_test.php'>🧪 Probar API</a></p>";

// Estadísticas actuales
$userCount = count(JsonDB::read(USERS_FILE));
$productCount = count(JsonDB::read(PRODUCTS_FILE));
$cartCount = count(JsonDB::read(CART_FILE));

echo "<br><h3>📊 Estadísticas Actuales</h3>";
echo "<ul>";
echo "<li>👥 Usuarios: $userCount</li>";
echo "<li>🕯️ Productos: $productCount</li>";
echo "<li>🛒 Items en carritos: $cartCount</li>";
echo "</ul>";
?>