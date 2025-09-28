<?php
/**
 * Configuración de Base de Datos MySQL
 * Migración de API externa a sistema interno
 */

// Configuración de la base de datos MySQL
define('DB_HOST', 'srv1508.hstgr.io'); // o 82.197.82.28
define('DB_NAME', 'u78538349_velaaroma');
define('DB_USER', 'u78538349_velaaroma');
define('DB_PASS', 'v3L44r0m4#');
define('DB_CHARSET', 'utf8mb4');

/**
 * Clase de conexión MySQL
 */
class MySQLDB {
    private static $pdo = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
    
    public static function query($sql, $params = []) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function fetchOne($sql, $params = []) {
        return self::query($sql, $params)->fetch();
    }
    
    public static function execute($sql, $params = []) {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function lastInsertId() {
        return self::getConnection()->lastInsertId();
    }
}

/**
 * Funciones auxiliares
 */
// Las funciones auxiliares se encuentran en utils.php

function generateValidationCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 9; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendEmail($to, $subject, $body) {
    // Por ahora solo log del email (puedes implementar PHPMailer más tarde)
    $emailData = [
        'to' => $to,
        'subject' => $subject,
        'body' => $body,
        'sent_at' => date('Y-m-d H:i:s')
    ];
    
    // Log en archivo temporal
    $logFile = __DIR__ . '/data/email_log.json';
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true) ?: [];
    }
    $logs[] = $emailData;
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    
    return true;
}

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');
?>