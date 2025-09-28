<?php
/**
 * Configuración de Base de Datos MySQL REAL
 * Datos reales de producción
 */

// Configuración de la base de datos MySQL REAL
define('DB_HOST', '82.197.82.28');
define('DB_NAME', 'u783538349_velaaroma');
define('DB_USER', 'u783538349_velaaroma');
define('DB_PASS', 'v3L44r0m4#');
define('DB_CHARSET', 'utf8mb4');

// Detectar si estamos en desarrollo local
define('IS_LOCAL_DEV', in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost:8000', '127.0.0.1:8000']));

/**
 * Clase de conexión MySQL con fallback para desarrollo local
 */
class MySQLDB {
    private static $pdo = null;
    private static $isConnected = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5, // Timeout de 5 segundos
                ]);
                self::$isConnected = true;
            } catch (PDOException $e) {
                self::$isConnected = false;
                // Mostrar error real y intentar soluciones
                error_log("Error de conexión detallado: " . $e->getMessage());
                error_log("Host: " . DB_HOST . ", DB: " . DB_NAME . ", User: " . DB_USER);
                
                if (IS_LOCAL_DEV) {
                    // En desarrollo local, informar del problema pero seguir
                    error_log("ADVERTENCIA: Usando datos simulados porque no hay conexión real a BD");
                    error_log("Para producción, todos los datos deben venir de la base de datos real");
                } else {
                    // En producción, fallar inmediatamente
                    die("Error de conexión a base de datos: " . $e->getMessage());
                }
            }
        }
        return self::$pdo;
    }
    
    public static function isConnected() {
        if (self::$isConnected === null) {
            self::getConnection();
        }
        return self::$isConnected;
    }
    
    public static function query($sql, $params = []) {
        if (self::isConnected()) {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } else {
            // Datos simulados para desarrollo
            return self::getMockData($sql, $params);
        }
    }
    
    public static function fetchAll($sql, $params = []) {
        if (self::isConnected()) {
            return self::query($sql, $params)->fetchAll();
        } else {
            return self::getMockData($sql, $params);
        }
    }
    
    public static function fetchOne($sql, $params = []) {
        if (self::isConnected()) {
            return self::query($sql, $params)->fetch();
        } else {
            $data = self::getMockData($sql, $params);
            return !empty($data) ? $data[0] : false;
        }
    }
    
    public static function execute($sql, $params = []) {
        if (self::isConnected()) {
            return self::query($sql, $params)->rowCount();
        } else {
            // Simular ejecución exitosa
            return 1;
        }
    }
    
    public static function lastInsertId() {
        if (self::isConnected()) {
            return self::getConnection()->lastInsertId();
        } else {
            return rand(1000, 9999);
        }
    }
    
    private static function getMockData($sql, $params = []) {
        $sql = strtolower(trim($sql));
        
        // Datos generados dinámicamente desde las imágenes
        if (str_contains($sql, 'va_list_product')) {
            return self::generateProductsFromImages($params);
        }
        
        // Datos simulados para usuarios
        if (str_contains($sql, 'va_users')) {
            if (str_contains($sql, 'where')) {
                // Login o validación
                return [
                    (object)[
                        'id' => 1,
                        'name' => 'Usuario',
                        'first_last_name' => 'Demo',
                        'username' => 'demo',
                        'virtual_address' => 'demo@test.com',
                        'virtual_address_is_validated' => '1'
                    ]
                ];
            } else {
                // Lista de usuarios
                return [
                    (object)[
                        'id' => 1,
                        'name' => 'Usuario Demo',
                        'username' => 'demo',
                        'virtual_address' => 'demo@test.com'
                    ]
                ];
            }
        }
        
        // Datos simulados para carrito
        if (str_contains($sql, 'va_cart')) {
            return [
                (object)[
                    'id' => 1,
                    'product_id' => 'Vela Aromática Lavanda',
                    'aroma' => 'Lavanda',
                    'color' => 'Blanco',
                    'cantidad' => 2,
                    'valor' => 130.00
                ]
            ];
        }
        
        return [];
    }
    
    private static function generateProductsFromImages($params = []) {
        $imagesPath = __DIR__ . '/../images/';
        $allProducts = [];
        $id = 1;
        
        // Categorías y sus patrones de imágenes
        $categories = [
            'figura_aroma' => ['figura_aroma_', 'figuras_aroma_'],
            'dia_de_muertos' => ['dia_de_muertos_'],
            'velas_vidrio' => ['velas_vidrio_'],
            'vela_yeso' => ['velas_yeso_'],
            'eventos' => ['eventos_', 'evento_'],
            'navidad' => ['navidad_', 'christmas_']
        ];
        
        // Aromas disponibles
        $aromas = ['Lavanda', 'Rosa', 'Vainilla', 'Jazmín', 'Coco', 'Canela', 'Eucalipto', 'Limón'];
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            
            foreach ($categories as $category => $patterns) {
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    
                    // Verificar si el archivo coincide con algún patrón de la categoría
                    $matchesPattern = false;
                    foreach ($patterns as $pattern) {
                        if (strpos($file, $pattern) === 0 && 
                            (pathinfo($file, PATHINFO_EXTENSION) === 'jpg' || 
                             pathinfo($file, PATHINFO_EXTENSION) === 'jpeg' || 
                             pathinfo($file, PATHINFO_EXTENSION) === 'png')) {
                            $matchesPattern = true;
                            break;
                        }
                    }
                    
                    if ($matchesPattern) {
                        // Extraer número del archivo para generar nombre único
                        preg_match('/\d+/', $file, $numbers);
                        $number = isset($numbers[0]) ? $numbers[0] : $id;
                        
                        // Generar nombre basado en categoría y número
                        $baseName = self::getCategoryDisplayName($category);
                        $aroma = $aromas[($id - 1) % count($aromas)];
                        $name = "$baseName $aroma #$number";
                        
                        // Precios variables según categoría
                        $prices = self::getCategoryPrices($category);
                        
                        // Medidas variables
                        $dimensions = self::getRandomDimensions();
                        
                        $allProducts[] = (object)[
                            'id' => $id,
                            'name' => $name,
                            'description' => "Vela aromática artesanal con fragancia de $aroma",
                            'mayoreo' => $prices['mayoreo'],
                            'menudeo' => $prices['menudeo'],
                            'largo' => $dimensions['largo'],
                            'alto' => $dimensions['alto'],
                            'ancho' => $dimensions['ancho'],
                            'category' => $category,
                            'url' => "/images/$file"
                        ];
                        
                        $id++;
                    }
                }
            }
        }
        
        // Si hay parámetros, filtrar por categoría
        if (!empty($params) && count($params) > 0) {
            $requestedCategory = $params[0];
            $filtered = array_filter($allProducts, function($product) use ($requestedCategory) {
                return $product->category === $requestedCategory;
            });
            return array_values($filtered);
        }
        
        return $allProducts;
    }
    
    private static function getCategoryDisplayName($category) {
        $names = [
            'figura_aroma' => 'Vela Aromática',
            'dia_de_muertos' => 'Vela Día de Muertos',
            'velas_vidrio' => 'Vela en Vidrio',
            'vela_yeso' => 'Vela de Yeso',
            'eventos' => 'Vela para Eventos',
            'navidad' => 'Vela Navideña'
        ];
        
        return $names[$category] ?? 'Vela Especial';
    }
    
    private static function getCategoryPrices($category) {
        $priceRanges = [
            'figura_aroma' => ['mayoreo' => rand(35, 50), 'menudeo' => rand(55, 75)],
            'dia_de_muertos' => ['mayoreo' => rand(30, 45), 'menudeo' => rand(45, 65)],
            'velas_vidrio' => ['mayoreo' => rand(45, 65), 'menudeo' => rand(70, 95)],
            'vela_yeso' => ['mayoreo' => rand(25, 40), 'menudeo' => rand(40, 60)],
            'eventos' => ['mayoreo' => rand(40, 55), 'menudeo' => rand(60, 80)],
            'navidad' => ['mayoreo' => rand(35, 50), 'menudeo' => rand(55, 75)]
        ];
        
        return $priceRanges[$category] ?? ['mayoreo' => 40, 'menudeo' => 60];
    }
    
    private static function getRandomDimensions() {
        return [
            'largo' => rand(8, 15),
            'alto' => rand(6, 12),
            'ancho' => rand(6, 12)
        ];
    }
}

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');
?>