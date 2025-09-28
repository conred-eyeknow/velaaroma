<?php
/**
 * Configuración de la Base de Datos JSON
 * Sistema simple sin complicaciones para Vela Aroma
 */

// Configuración de rutas de datos
define('DATA_DIR', __DIR__ . '/data/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('PRODUCTS_FILE', DATA_DIR . 'products.json');
define('CART_FILE', DATA_DIR . 'cart.json');
define('SESSIONS_FILE', DATA_DIR . 'sessions.json');

// Crear directorio de datos si no existe
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

/**
 * Clase simple para manejar datos JSON
 */
class JsonDB {
    
    public static function read($file) {
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }
    
    public static function write($file, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($file, $json, LOCK_EX);
    }
    
    public static function generateId() {
        return uniqid() . '_' . time();
    }
    
    public static function findById($data, $id) {
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }
    
    public static function findByField($data, $field, $value) {
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                return $item;
            }
        }
        return null;
    }
    
    public static function removeById(&$data, $id) {
        foreach ($data as $key => $item) {
            if ($item['id'] == $id) {
                unset($data[$key]);
                return true;
            }
        }
        return false;
    }
}

/**
 * Funciones auxiliares
 */
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit();
    }
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateCode() {
    return sprintf('%06d', mt_rand(100000, 999999));
}

// Inicializar archivos si no existen
$initialData = [
    USERS_FILE => [],
    PRODUCTS_FILE => [],
    CART_FILE => [],
    SESSIONS_FILE => []
];

foreach ($initialData as $file => $data) {
    if (!file_exists($file)) {
        JsonDB::write($file, $data);
    }
}
?>