<?php
/**
 * Controlador de Carrito MySQL
 * Migrado de la API externa original
 */

class MySQLCartController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case 'products':
                if ($method === 'GET') {
                    if ($param === 'sell') {
                        $this->obtainProductsSell();
                    } else {
                        $this->obtainProducts();
                    }
                } elseif ($method === 'POST') {
                    if ($param === 'sell') {
                        $this->processCartSale();
                    } else {
                        $this->addProducts();
                    }
                }
                break;
                
            case 'update_status':
                $this->updateStatusCartv2();
                break;
                
            default:
                sendJsonResponse(['error' => 'Acción no encontrada'], 404);
        }
    }
    
    public function obtainProducts() {
        $username = $_GET["username"] ?? '';
        $status = $_GET["status"] ?? 'in_progress';
        
        if (empty($username)) {
            sendJsonResponse(['products' => 0]);
            return;
        }
        
        $sql = "SELECT * FROM cart WHERE username = ? AND status = ? AND deleted_at IS NULL";
        $result = MySQLDB::fetchAll($sql, [$username, $status]);
        
        sendJsonResponse(['products' => count($result)]);
    }
    
    public function obtainProductsSell() {
        $username = $_GET["username"] ?? '';
        $status = $_GET["status"] ?? 'in_progress';
        
        if (empty($username)) {
            sendJsonResponse(['products' => []]);
            return;
        }
        
        $sql = "SELECT c.id, lp.name as product_id, c.aroma, c.color, c.cantidad, c.valor 
                FROM cart c 
                INNER JOIN list_product lp ON c.product_id = lp.id 
                WHERE c.username = ? AND c.status = ? AND c.deleted_at IS NULL";
        $result = MySQLDB::fetchAll($sql, [$username, $status]);
        
        sendJsonResponse(['products' => $result]);
    }
    
    public function addProducts() {
        $data = $this->getPostData();
        
        $username = $data["username"] ?? '';
        $status = $data["status"] ?? 'in_progress';
        $color = $data["color"] ?? 'Blanco';
        $aroma = $data["aroma"] ?? 'Lavanda';
        $cantidad = intval($data["cantidad"] ?? 1);
        $product_id = $data["product_id"] ?? '';
        
        if (empty($username) || empty($product_id)) {
            sendJsonResponse(['error' => 'Username y product_id requeridos'], 400);
        }
        
        // Verificar si el producto ya está en el carrito
        $sql = "SELECT * FROM cart WHERE product_id = ? AND aroma = ? AND color = ? AND username = ? AND status = 'in_progress' AND deleted_at IS NULL";
        $existingProduct = MySQLDB::fetchAll($sql, [$product_id, $aroma, $color, $username]);
        
        if (empty($existingProduct)) {
            // Obtener precios del producto
            $sql = "SELECT * FROM list_product WHERE id = ?";
            $productInfo = MySQLDB::fetchOne($sql, [$product_id]);
            
            if (!$productInfo) {
                sendJsonResponse(['error' => 'Producto no encontrado'], 404);
            }
            
            $mayoreo = $productInfo->mayoreo;
            $menudeo = $productInfo->menudeo;
            
            // Calcular valor
            $valor = ($cantidad > 4) ? $cantidad * $mayoreo : $cantidad * $menudeo;
            
            // Insertar nuevo producto al carrito
            $sql = "INSERT INTO cart (username, product_id, color, aroma, cantidad, valor, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            MySQLDB::execute($sql, [$username, $product_id, $color, $aroma, $cantidad, $valor, $status]);
            
        } else {
            // Actualizar cantidad existente
            $newCantidad = $existingProduct[0]->cantidad + $cantidad;
            $cart_id = $existingProduct[0]->id;
            
            // Obtener precios del producto
            $sql = "SELECT * FROM list_product WHERE id = ?";
            $productInfo = MySQLDB::fetchOne($sql, [$product_id]);
            
            $mayoreo = $productInfo->mayoreo;
            $menudeo = $productInfo->menudeo;
            
            // Calcular nuevo valor
            $valor = ($newCantidad > 3) ? $newCantidad * $mayoreo : $newCantidad * $menudeo;
            
            // Actualizar carrito
            $sql = "UPDATE cart SET cantidad = ?, valor = ? WHERE id = ?";
            MySQLDB::execute($sql, [$newCantidad, $valor, $cart_id]);
        }
        
        sendJsonResponse([
            'message' => 'Producto agregado al carrito exitosamente',
            'data' => 'OK'
        ]);
    }
    
    public function processCartSale() {
        $data = $this->getPostData();
        
        $username = $data["username"] ?? '';
        
        if (empty($username)) {
            sendJsonResponse(['error' => 'Username requerido'], 400);
        }
        
        try {
            // Obtener productos del carrito
            $sql = "SELECT c.id, lp.name as product_id, c.aroma, c.color, c.cantidad, c.valor, lp.menudeo, lp.mayoreo
                    FROM cart c 
                    INNER JOIN list_product lp ON c.product_id = lp.id 
                    WHERE c.username = ? AND c.status = 'in_progress' AND c.deleted_at IS NULL";
            $cartItems = MySQLDB::fetchAll($sql, [$username]);
            
            if (empty($cartItems)) {
                sendJsonResponse(['error' => 'Carrito vacío'], 400);
            }
            
            // Calcular totales
            $totalQuantity = 0;
            $totalValue = 0;
            
            foreach ($cartItems as $item) {
                $totalQuantity += $item->cantidad;
                $totalValue += $item->valor;
            }
            
            // Marcar productos como completados
            $sql = "UPDATE cart SET status = 'completed' WHERE status = 'in_progress' AND username = ?";
            MySQLDB::execute($sql, [$username]);
            
            // Obtener email del usuario para envío
            $sql = "SELECT email FROM users WHERE username = ?";
            $user = MySQLDB::fetchOne($sql, [$username]);
            
            if ($user) {
                $email = $user->email . ",ivan.balderas.serrano@gmail.com";
                $subject = "Vela Aroma | Compra exitosa";
                $emailBody = $this->generatePurchaseEmail($username, $totalQuantity, $totalValue, $cartItems);
                sendEmail($email, $subject, $emailBody);
            }
            
            sendJsonResponse([
                'message' => 'Venta procesada exitosamente',
                'total_quantity' => $totalQuantity,
                'total_value' => $totalValue,
                'items' => $cartItems,
                'result' => 'El correo electrónico ha sido enviado.'
            ]);
            
        } catch (Exception $e) {
            sendJsonResponse(['error' => 'Error procesando la venta: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateStatusCartv2() {
        $data = $this->getPostData();
        
        $username = $data["username"] ?? '';
        $status = $data["status"] ?? '';
        $product_id = $data["product_id"] ?? '';
        
        if (empty($username) || empty($status)) {
            sendJsonResponse(['error' => 'Username y status requeridos'], 400);
        }
        
        if (!empty($product_id)) {
            // Actualizar producto específico
            $sql = "UPDATE cart SET status = ? WHERE id = ? AND username = ?";
            MySQLDB::execute($sql, [$status, $product_id, $username]);
        } else {
            // Actualizar todos los productos del usuario
            $sql = "UPDATE cart SET status = ? WHERE username = ? AND status = 'in_progress'";
            MySQLDB::execute($sql, [$status, $username]);
        }
        
        sendJsonResponse([
            'message' => 'Status actualizado exitosamente',
            'update' => "Status cambiado a $status"
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
    
    private function generatePurchaseEmail($username, $totalQuantity, $totalValue, $items) {
        $itemsList = '';
        foreach ($items as $item) {
            $itemsList .= "<li>{$item->product_id} - Color: {$item->color}, Aroma: {$item->aroma}, Cantidad: {$item->cantidad}, Valor: $\${$item->valor}</li>";
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head><title>Confirmación de compra</title></head>
        <body>
            <h1>Gracias por tu compra en Vela Aroma, '.$username.'!</h1>
            <h2>Detalles de tu compra:</h2>
            <ul>'.$itemsList.'</ul>
            <p><strong>Número de productos:</strong> '.$totalQuantity.'</p>
            <p><strong>Total:</strong> $'.$totalValue.'</p>
            <p>Si tienes alguna pregunta sobre tu compra, no dudes en contactarnos.</p>
        </body>
        </html>';
    }
}
?>