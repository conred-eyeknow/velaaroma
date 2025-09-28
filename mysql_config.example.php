<?php
/**
 * PLANTILLA DE CONFIGURACIÓN MYSQL - PRODUCCIÓN
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo como 'mysql_config.php' en el servidor de producción
 * 2. Actualiza las credenciales de base de datos reales
 * 3. NUNCA subas este archivo a git (está en .gitignore)
 */

// Configuración de la base de datos MySQL REAL
define('DB_HOST', '82.197.82.28'); // Tu IP de base de datos
define('DB_NAME', 'u783538349_velaaroma'); // Tu base de datos
define('DB_USER', 'u783538349_velaaroma'); // Tu usuario
define('DB_PASS', 'TU_PASSWORD_AQUI'); // ⚠️ CAMBIAR POR LA CONTRASEÑA REAL
define('DB_CHARSET', 'utf8mb4');

// Detectar si estamos en desarrollo local
define('IS_LOCAL_DEV', in_array($_SERVER['HTTP_HOST'] ?? 'localhost', [
    'localhost', 'localhost:8000', 'localhost:8001', 'localhost:8002', 
    'localhost:8003', 'localhost:8004', '127.0.0.1', '127.0.0.1:8000'
]) || php_sapi_name() === 'cli');

/**
 * Clase de conexión MySQL REAL
 */
class MySQLDB {
    private static $connection = null;
    private static $isConnected = false;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 10
                    ]
                );
                self::$isConnected = true;
                error_log("VelaAroma DB: Conexión exitosa a base de datos real");
            } catch (PDOException $e) {
                self::$isConnected = false;
                if (IS_LOCAL_DEV) {
                    error_log("VelaAroma DB: Error conexión local: " . $e->getMessage());
                } else {
                    error_log("VelaAroma DB: ERROR CRÍTICO en producción: " . $e->getMessage());
                    throw new Exception("Error de conexión a base de datos en producción");
                }
            }
        }
        return self::$connection;
    }
    
    public static function isConnected() {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$isConnected;
    }
    
    public static function testConnection() {
        try {
            if (!self::isConnected()) {
                return false;
            }
            $stmt = self::$connection->prepare("SELECT 1 as test");
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("VelaAroma DB: Error en test de conexión: " . $e->getMessage());
            return false;
        }
    }
    
    public static function fetchAll($query, $params = []) {
        if (!self::isConnected()) {
            return self::getSimulatedData();
        }
        
        try {
            $stmt = self::$connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("VelaAroma DB: Error en fetchAll: " . $e->getMessage());
            return self::getSimulatedData();
        }
    }
    
    public static function fetchOne($query, $params = []) {
        if (!self::isConnected()) {
            return (object)['count' => 0];
        }
        
        try {
            $stmt = self::$connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("VelaAroma DB: Error en fetchOne: " . $e->getMessage());
            return (object)['count' => 0];
        }
    }
    
    public static function execute($query, $params = []) {
        if (!self::isConnected()) {
            throw new Exception("No hay conexión a la base de datos");
        }
        
        try {
            $stmt = self::$connection->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("VelaAroma DB: Error en execute: " . $e->getMessage());
            throw $e;
        }
    }
    
    private static function getSimulatedData() {
        error_log("VelaAroma DB: ADVERTENCIA - Usando datos simulados");
        return [
            (object)[
                'id' => 1,
                'nombre' => 'Producto Simulado 1',
                'categoria' => 'test',
                'precio' => 99.99,
                'imagen' => '/images/figura_aroma_1.jpg'
            ],
            (object)[
                'id' => 2,
                'nombre' => 'Producto Simulado 2', 
                'categoria' => 'test',
                'precio' => 149.99,
                'imagen' => '/images/figura_aroma_2.jpg'
            ]
        ];
    }
}

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');
?>