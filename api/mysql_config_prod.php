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

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');
?>