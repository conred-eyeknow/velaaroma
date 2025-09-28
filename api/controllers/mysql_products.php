<?php
/**
 * Controlador de Productos MySQL
 * Migrado de la API externa original
 */

class MySQLProductsController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case '': // GET /api/products - listar productos
                if ($method === 'GET') {
                    $this->obtainProductsAll();
                } elseif ($method === 'POST') {
                    $this->productsCreate();
                }
                break;
                
            case 'category':
                $this->obtainProductsByCategory();
                break;
                
            case 'update':
                $this->productsUpdate();
                break;
                
            case 'create':
                $this->productsCreate();
                break;
                
            default:
                // Verificar si es un ID específico
                if (is_numeric($action)) {
                    $this->getProduct($action);
                } else {
                    sendJsonResponse(['error' => 'Acción no encontrada'], 404);
                }
        }
    }
    
    public function obtainProductsAll() {
        $sql = "SELECT * FROM list_product WHERE (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
        $result = MySQLDB::fetchAll($sql);
        
        sendJsonResponse(['products' => $result]);
    }
    
    public function getProduct($id) {
        $sql = "SELECT * FROM list_product WHERE id = ? AND (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
        $result = MySQLDB::fetchOne($sql, [$id]);
        
        if (!$result) {
            sendJsonResponse(['error' => 'Producto no encontrado'], 404);
        }
        
        sendJsonResponse(['product' => $result]);
    }
    
    public function obtainProductsByCategory() {
        $category = $_GET['category'] ?? '';
        
        if (empty($category)) {
            sendJsonResponse(['error' => 'Categoría requerida'], 400);
        }
        
        $sql = "SELECT * FROM list_product WHERE category = ? AND (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
        $result = MySQLDB::fetchAll($sql, [$category]);
        
        sendJsonResponse(['products' => $result]);
    }
    
    public function productsCreate() {
        $data = $this->getPostData();
        
        // Validar campos requeridos
        $required = ['name', 'mayoreo', 'menudeo', 'category'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                sendJsonResponse(['error' => "Campo requerido: $field"], 400);
            }
        }
        
        $name = $data["name"];
        $description = $data["description"] ?? '';
        $mayoreo = $data["mayoreo"];
        $menudeo = $data["menudeo"];
        $largo = $data["largo"] ?? '0';
        $alto = $data["alto"] ?? '0';
        $ancho = $data["ancho"] ?? '0';
        $category = $data["category"];
        $url = $data["url"] ?? '';
        
        // Insertar producto
        $sql = "INSERT INTO list_product (name, description, mayoreo, menudeo, largo, alto, ancho, category, url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        MySQLDB::execute($sql, [$name, $description, $mayoreo, $menudeo, $largo, $alto, $ancho, $category, $url]);
        
        // Obtener el producto recién creado
        $sql = "SELECT * FROM list_product WHERE name = ? AND mayoreo = ? AND category = ? ORDER BY id DESC LIMIT 1";
        $result = MySQLDB::fetchAll($sql, [$name, $mayoreo, $category]);
        
        sendJsonResponse([
            'message' => 'Producto creado exitosamente',
            'products' => $result
        ]);
    }
    
    public function productsUpdate() {
        $data = $this->getPostData();
        
        $column = $data["column"] ?? '';
        $id = $data["id"] ?? '';
        $value = $data["val"] ?? $data["value"] ?? '';
        
        if (empty($column) || empty($id)) {
            sendJsonResponse(['error' => 'ID y columna requeridos'], 400);
        }
        
        // Validar columnas permitidas por seguridad
        $allowedColumns = ['name', 'description', 'mayoreo', 'menudeo', 'largo', 'alto', 'ancho', 'category', 'url'];
        if (!in_array($column, $allowedColumns)) {
            sendJsonResponse(['error' => 'Columna no permitida'], 400);
        }
        
        $sql = "UPDATE list_product SET $column = ? WHERE id = ?";
        MySQLDB::execute($sql, [$value, $id]);
        
        sendJsonResponse([
            'message' => 'Producto actualizado exitosamente',
            'update' => "Columna $column actualizada a $value"
        ]);
    }
    
    private function getPostData() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $_POST;
            
            if (empty($data) && !empty($input)) {
                parse_str($input, $data);
            }
        }
        
        return $data ?: [];
    }
}
?>