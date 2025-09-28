<?php
/**
 * Configuración de Rutas de API
 * Define las rutas disponibles para las operaciones CRUD
 */

// Configuración base
define('API_BASE_PATH', '/api');
define('UPLOAD_DIR', '../images/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

// Rutas de API disponibles
$API_ROUTES = [
    // Productos
    'GET /api/products' => 'products/list.php',
    'GET /api/products/search' => 'products/search.php',
    'POST /api/products/create' => 'products/create.php',
    'POST /api/products/create-with-image' => 'products/create-with-image.php',
    'PUT /api/products/update' => 'products/update.php',
    'DELETE /api/products/delete' => 'products/delete.php',
    
    // Usuarios
    'POST /api/users/register' => 'users/register.php',
    'POST /api/users/login' => 'users/login.php',
    'POST /api/users/logout' => 'users/logout.php',
    'POST /api/users/forgot-password' => 'users/forgot-password.php',
    'POST /api/users/reset-password' => 'users/reset-password.php',
    
    // Carrito
    'GET /api/cart' => 'cart/list.php',
    'POST /api/cart/add' => 'cart/add.php',
    'PUT /api/cart/update' => 'cart/update.php',
    'DELETE /api/cart/remove' => 'cart/remove.php',
    'DELETE /api/cart/clear' => 'cart/clear.php',
];

/**
 * Funciones de utilidad para manejo de archivos
 */
function createUploadDirectory() {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
}

function validateImageFile($file) {
    // Verificar errores de subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error en la subida del archivo');
    }
    
    // Verificar tipo de archivo
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        throw new Exception('Tipo de archivo no permitido. Solo JPG, PNG y GIF.');
    }
    
    // Verificar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('El archivo es muy grande. Máximo 5MB.');
    }
    
    return true;
}

/**
 * Generar nombre de archivo único basado en categoría
 */
function generateCategoryFilename($category, $extension) {
    $prefixes = [
        'figura_aroma' => 'figura_aroma_',
        'navidad' => 'navidad_',
        'dia_de_muertos' => 'dia_de_muertos_',
        'velas_vidrio' => 'velas_vidrio_',
        'velas_yeso' => 'velas_yeso_',
        'eventos' => 'eventos_',
        'buda' => 'buda_'
    ];
    
    $prefix = $prefixes[strtolower($category)] ?? 'producto_';
    
    // Buscar el siguiente número disponible
    $counter = 1;
    $pattern = UPLOAD_DIR . $prefix . '*.' . strtolower($extension);
    $existingFiles = glob($pattern);
    
    if (!empty($existingFiles)) {
        $numbers = [];
        foreach ($existingFiles as $file) {
            $basename = basename($file, '.' . strtolower($extension));
            $number = str_replace($prefix, '', $basename);
            if (is_numeric($number)) {
                $numbers[] = intval($number);
            }
        }
        if (!empty($numbers)) {
            $counter = max($numbers) + 1;
        }
    }
    
    return $prefix . $counter . '.' . strtolower($extension);
}

/**
 * Respuestas de API estandarizadas
 */
function apiSuccess($data = null, $message = 'Operación exitosa') {
    return [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ];
}

function apiError($message, $code = 500) {
    http_response_code($code);
    return [
        'success' => false,
        'error' => $message,
        'timestamp' => date('c')
    ];
}

/**
 * Headers de respuesta común
 */
function setApiHeaders() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit();
    }
}

?>