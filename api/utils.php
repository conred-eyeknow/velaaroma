<?php
/**
 * Funciones auxiliares para la API interna
 * Migrado de la API externa original
 */

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function generateValidationCode($length = 40) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

function sendEmail($to, $subject, $body) {
    // Configurar PHPMailer (si está disponible)
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración del servidor SMTP (ejemplo con Gmail)
            $mail->isSMTP();
            $mail->Host = 'localhost'; // O tu servidor SMTP preferido
            $mail->SMTPAuth = false;
            $mail->Port = 1025; // Puerto para desarrollo local
            
            // Configurar codificación
            $mail->CharSet = 'UTF-8';
            
            // Remitente
            $mail->setFrom('noreply@velaaroma.com', 'Vela Aroma');
            
            // Destinatarios
            $recipients = explode(',', $to);
            foreach ($recipients as $recipient) {
                $mail->addAddress(trim($recipient));
            }
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    } else {
        // Fallback usando mail() de PHP
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Vela Aroma <noreply@velaaroma.com>' . "\r\n";
        
        return mail($to, $subject, $body, $headers);
    }
}

function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Context: " . json_encode($context);
    }
    error_log($logMessage);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function isValidPassword($password) {
    // Mínimo 6 caracteres
    return strlen($password) >= 6;
}

function isValidUsername($username) {
    // Solo letras, números y guiones bajos, mínimo 3 caracteres
    return preg_match('/^[a-zA-Z0-9_]{3,}$/', $username);
}

function calculateCartValue($quantity, $wholesalePrice, $retailPrice) {
    // Si la cantidad es mayor a 3, usar precio mayoreo
    return ($quantity > 3) ? $quantity * $wholesalePrice : $quantity * $retailPrice;
}

function formatCurrency($amount) {
    return number_format($amount, 2, '.', '');
}

function getCurrentTimestamp() {
    return date('Y-m-d H:i:s');
}

function debugLog($data, $label = 'DEBUG') {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log($label . ': ' . print_r($data, true));
    }
}

function corsHeaders() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function validateRequiredFields($data, $requiredFields) {
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

function generateUniqueId($prefix = '') {
    return $prefix . uniqid() . random_int(1000, 9999);
}

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function rateLimitCheck($identifier, $maxRequests = 100, $timeWindow = 3600) {
    // Implementación básica de rate limiting
    // En producción se podría usar Redis o memcached
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if (time() - $data['timestamp'] < $timeWindow) {
            if ($data['count'] >= $maxRequests) {
                return false;
            }
            $data['count']++;
        } else {
            $data = ['count' => 1, 'timestamp' => time()];
        }
    } else {
        $data = ['count' => 1, 'timestamp' => time()];
    }
    
    file_put_contents($cacheFile, json_encode($data));
    return true;
}

function securityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function cleanOldFiles($directory, $maxAge = 86400) {
    // Limpiar archivos temporales más antiguos que $maxAge segundos
    if (!is_dir($directory)) {
        return;
    }
    
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $directory . '/' . $file;
        if (is_file($filePath) && (time() - filemtime($filePath)) > $maxAge) {
            unlink($filePath);
        }
    }
}
?>