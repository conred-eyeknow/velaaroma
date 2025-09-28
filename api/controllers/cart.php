<?php
/**
 * Controlador de Carrito
 * Maneja el carrito de compras y órdenes
 */

class CartController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case 'products':
                if ($method === 'GET') {
                    if ($param === 'sell') {
                        $this->sellProducts();
                    } elseif ($param === 'category') {
                        // Redirigir a productos por categoría
                        require_once 'products.php';
                        $productController = new ProductsController();
                        $productController->getProductsByCategory();
                    } else {
                        $this->getCartProducts();
                    }
                } elseif ($method === 'POST') {
                    if ($param === 'sell') {
                        $this->sellProducts();
                    } else {
                        $this->addToCart();
                    }
                }
                break;
                
            case 'update_status':
                $this->updateCartStatus();
                break;
                
            default:
                sendJsonResponse(['error' => 'Acción no encontrada'], 404);
        }
    }
    
    public function addToCart() {
        $data = $this->getPostData();
        
        // Validar campos requeridos
        $required = ['username', 'product_id', 'cantidad'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendJsonResponse(['error' => "Campo requerido: $field"], 400);
            }
        }
        
        $cart = JsonDB::read(CART_FILE);
        
        $cartItem = [
            'id' => JsonDB::generateId(),
            'username' => $data['username'],
            'product_id' => $data['product_id'],
            'cantidad' => intval($data['cantidad']),
            'color' => $data['color'] ?? 'Blanco',
            'aroma' => $data['aroma'] ?? 'Lavanda',
            'status' => $data['status'] ?? 'in_progress',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $cart[] = $cartItem;
        JsonDB::write(CART_FILE, $cart);
        
        sendJsonResponse(['message' => 'Producto agregado al carrito', 'item' => $cartItem]);
    }
    
    public function getCartProducts() {
        $username = $_GET['username'] ?? '';
        $status = $_GET['status'] ?? 'in_progress';
        
        if (empty($username)) {
            sendJsonResponse(['products' => 0]);
            return;
        }
        
        $cart = JsonDB::read(CART_FILE);
        $userCart = [];
        $totalItems = 0;
        
        foreach ($cart as $item) {
            if ($item['username'] === $username && $item['status'] === $status) {
                $userCart[] = $item;
                $totalItems += $item['cantidad'];
            }
        }
        
        sendJsonResponse(['products' => $totalItems, 'items' => $userCart]);
    }
    
    public function sellProducts() {
        $data = $this->getPostData();
        
        if (empty($data['username'])) {
            sendJsonResponse(['error' => 'Username requerido'], 400);
        }
        
        $cart = JsonDB::read(CART_FILE);
        $products = JsonDB::read(PRODUCTS_FILE);
        
        $userCart = [];
        $total = 0;
        
        // Obtener productos del carrito del usuario
        foreach ($cart as &$item) {
            if ($item['username'] === $data['username'] && $item['status'] === 'in_progress') {
                
                // Buscar información del producto
                $product = JsonDB::findById($products, $item['product_id']);
                if ($product) {
                    $subtotal = $product['menudeo'] * $item['cantidad'];
                    $item['product_name'] = $product['name'];
                    $item['unit_price'] = $product['menudeo'];
                    $item['subtotal'] = $subtotal;
                    $total += $subtotal;
                    
                    $userCart[] = $item;
                }
            }
        }
        
        if (empty($userCart)) {
            sendJsonResponse(['error' => 'Carrito vacío'], 400);
        }
        
        // Crear orden
        $orders = JsonDB::read(DATA_DIR . 'orders.json');
        $order = [
            'id' => JsonDB::generateId(),
            'username' => $data['username'],
            'items' => $userCart,
            'total' => $total,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $orders[] = $order;
        JsonDB::write(DATA_DIR . 'orders.json', $orders);
        
        // Marcar items del carrito como vendidos
        foreach ($cart as &$item) {
            if ($item['username'] === $data['username'] && $item['status'] === 'in_progress') {
                $item['status'] = 'sold';
                $item['order_id'] = $order['id'];
            }
        }
        
        JsonDB::write(CART_FILE, $cart);
        
        sendJsonResponse([
            'message' => 'Venta procesada exitosamente',
            'order' => $order
        ]);
    }
    
    public function updateCartStatus() {
        $data = $this->getPostData();
        
        if (empty($data['username']) || empty($data['status'])) {
            sendJsonResponse(['error' => 'Username y status requeridos'], 400);
        }
        
        $cart = JsonDB::read(CART_FILE);
        $updated = 0;
        
        foreach ($cart as &$item) {
            if ($item['username'] === $data['username']) {
                $item['status'] = $data['status'];
                $item['updated_at'] = date('Y-m-d H:i:s');
                $updated++;
            }
        }
        
        JsonDB::write(CART_FILE, $cart);
        
        sendJsonResponse([
            'message' => 'Status actualizado',
            'updated_items' => $updated
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