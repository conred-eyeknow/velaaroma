<?php
/**
 * Controlador de Productos
 * Maneja el catálogo de productos, categorías y CRUD
 */

class ProductsController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case '': // GET /api/products - listar productos
                if ($method === 'GET') {
                    $this->listProducts();
                } elseif ($method === 'POST') {
                    $this->createProduct();
                }
                break;
                
            case 'category':
                $this->getProductsByCategory();
                break;
                
            case 'update':
                $this->updateProduct();
                break;
                
            case 'create':
                $this->createProduct();
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
    
    public function listProducts() {
        $products = JsonDB::read(PRODUCTS_FILE);
        sendJsonResponse(['products' => $products]);
    }
    
    public function getProduct($id) {
        $products = JsonDB::read(PRODUCTS_FILE);
        $product = JsonDB::findById($products, $id);
        
        if (!$product) {
            sendJsonResponse(['error' => 'Producto no encontrado'], 404);
        }
        
        sendJsonResponse(['product' => $product]);
    }
    
    public function getProductsByCategory() {
        $category = $_GET['category'] ?? '';
        
        if (empty($category)) {
            sendJsonResponse(['error' => 'Categoría requerida'], 400);
        }
        
        $products = JsonDB::read(PRODUCTS_FILE);
        $filteredProducts = [];
        
        foreach ($products as $product) {
            if ($product['category'] === $category) {
                $filteredProducts[] = $product;
            }
        }
        
        sendJsonResponse(['products' => $filteredProducts]);
    }
    
    public function createProduct() {
        $this->initializeDefaultProducts();
        
        $data = $this->getPostData();
        
        // Validar campos requeridos
        $required = ['name', 'category', 'menudeo', 'mayoreo'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                sendJsonResponse(['error' => "Campo requerido: $field"], 400);
            }
        }
        
        $products = JsonDB::read(PRODUCTS_FILE);
        
        $product = [
            'id' => JsonDB::generateId(),
            'name' => $data['name'],
            'category' => $data['category'],
            'menudeo' => floatval($data['menudeo']),
            'mayoreo' => floatval($data['mayoreo']),
            'alto' => $data['alto'] ?? '0',
            'ancho' => $data['ancho'] ?? '0',
            'largo' => $data['largo'] ?? '0',
            'url' => $data['url'] ?? $this->getDefaultImageUrl($data['category']),
            'description' => $data['description'] ?? '',
            'stock' => $data['stock'] ?? 0,
            'active' => $data['active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $products[] = $product;
        JsonDB::write(PRODUCTS_FILE, $products);
        
        sendJsonResponse(['message' => 'Producto creado exitosamente', 'product' => $product]);
    }
    
    public function updateProduct() {
        $data = $this->getPostData();
        
        if (empty($data['id'])) {
            sendJsonResponse(['error' => 'ID del producto requerido'], 400);
        }
        
        $products = JsonDB::read(PRODUCTS_FILE);
        
        foreach ($products as &$product) {
            if ($product['id'] === $data['id']) {
                // Actualizar solo los campos proporcionados
                $updatableFields = ['name', 'category', 'menudeo', 'mayoreo', 'alto', 'ancho', 'largo', 'url', 'description', 'stock', 'active'];
                
                foreach ($updatableFields as $field) {
                    if (isset($data[$field])) {
                        $product[$field] = $data[$field];
                    }
                }
                
                $product['updated_at'] = date('Y-m-d H:i:s');
                
                JsonDB::write(PRODUCTS_FILE, $products);
                sendJsonResponse(['message' => 'Producto actualizado exitosamente', 'product' => $product]);
                return;
            }
        }
        
        sendJsonResponse(['error' => 'Producto no encontrado'], 404);
    }
    
    private function initializeDefaultProducts() {
        $products = JsonDB::read(PRODUCTS_FILE);
        
        // Si ya hay productos, no inicializar
        if (count($products) > 0) {
            return;
        }
        
        // Productos de ejemplo para cada categoría
        $defaultProducts = [
            // Velas con figuras
            [
                'id' => 'figura_1',
                'name' => 'Vela Figura Buda',
                'category' => 'figura_aroma',
                'menudeo' => 85,
                'mayoreo' => 65,
                'alto' => '12',
                'ancho' => '8',
                'largo' => '6',
                'url' => './images/buda.jpg',
                'description' => 'Vela aromática con figura de Buda',
                'stock' => 50,
                'active' => 1
            ],
            [
                'id' => 'figura_2',
                'name' => 'Vela Figura Decorativa 1',
                'category' => 'figura_aroma',
                'menudeo' => 75,
                'mayoreo' => 55,
                'alto' => '10',
                'ancho' => '7',
                'largo' => '5',
                'url' => './images/figura_aroma_2.jpg',
                'description' => 'Vela aromática decorativa',
                'stock' => 30,
                'active' => 1
            ],
            // Velas de yeso
            [
                'id' => 'yeso_1',
                'name' => 'Vela Recipiente Yeso Clásico',
                'category' => 'velas_yeso',
                'menudeo' => 95,
                'mayoreo' => 75,
                'alto' => '8',
                'ancho' => '8',
                'largo' => '8',
                'url' => './images/velas_yeso_1.jpg',
                'description' => 'Vela en recipiente de yeso artesanal',
                'stock' => 25,
                'active' => 1
            ],
            // Velas de vidrio
            [
                'id' => 'vidrio_1',
                'name' => 'Vela Recipiente Vidrio Elegante',
                'category' => 'velas_vidrio',
                'menudeo' => 120,
                'mayoreo' => 95,
                'alto' => '10',
                'ancho' => '7',
                'largo' => '7',
                'url' => './images/velas_vidrio_1.jpg',
                'description' => 'Vela en elegante recipiente de vidrio',
                'stock' => 40,
                'active' => 1
            ],
            // Día de muertos
            [
                'id' => 'muertos_1',
                'name' => 'Vela Día de Muertos Cempasúchil',
                'category' => 'dia_de_muertos',
                'menudeo' => 65,
                'mayoreo' => 45,
                'alto' => '6',
                'ancho' => '6',
                'largo' => '6',
                'url' => './images/dia_de_muertos_1.jpg',
                'description' => 'Vela especial para Día de Muertos con aroma a cempasúchil',
                'stock' => 60,
                'active' => 1
            ]
        ];
        
        foreach ($defaultProducts as &$product) {
            $product['created_at'] = date('Y-m-d H:i:s');
        }
        
        JsonDB::write(PRODUCTS_FILE, $defaultProducts);
    }
    
    private function getDefaultImageUrl($category) {
        $defaultImages = [
            'figura_aroma' => './images/figura_aroma_2.jpg',
            'velas_yeso' => './images/velas_yeso_1.jpg',
            'velas_vidrio' => './images/velas_vidrio_1.jpg',
            'dia_de_muertos' => './images/dia_de_muertos_1.jpg',
            'navidad' => './images/figura_aroma_35.jpg',
            'eventos' => './images/figura_aroma_36.jpg'
        ];
        
        return $defaultImages[$category] ?? './images/figura_aroma_2.jpg';
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