<?php
/**
 * Archivo de configuración para desarrollo local
 * Vela Aroma - Ambiente de Desarrollo
 */

// Configuración de entorno
define('ENVIRONMENT', 'development'); // production | development | testing

// Configuración de API
define('API_BASE_URL', 'https://api.velaaroma.com/v1/');
define('API_VERSION', 'v1');

// URLs base
define('BASE_URL', 'http://localhost:8000');
define('ASSETS_URL', BASE_URL);

// Configuración de debugging
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Headers CORS para desarrollo local
if (ENVIRONMENT === 'development') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Configuración de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Funciones auxiliares para desarrollo
function isLocal() {
    return ENVIRONMENT === 'development';
}

function getApiUrl($endpoint = '') {
    return API_BASE_URL . ltrim($endpoint, '/');
}

function getBaseUrl($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Log de debug para desarrollo
function debugLog($message, $data = null) {
    if (isLocal()) {
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
        if ($data !== null) {
            $logMessage .= ' - ' . json_encode($data);
        }
        error_log($logMessage);
    }
}

// Información del entorno actual
if (isLocal()) {
    debugLog('Iniciando aplicación en modo desarrollo', [
        'php_version' => PHP_VERSION,
        'base_url' => BASE_URL,
        'api_url' => API_BASE_URL
    ]);
}
?>