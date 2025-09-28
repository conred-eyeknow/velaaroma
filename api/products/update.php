<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir solo las funciones, no ejecutar el código
function handleImageUpload($file, $category) {
    // Configuración - usar la carpeta images existente en la raíz
    $uploadDir = __DIR__ . '/../../images/';
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de archivo no permitido. Solo JPG, PNG y GIF.');
    }
    
    // Validar tamaño
    if ($file['size'] > $maxFileSize) {
        throw new Exception('El archivo es muy grande. Máximo 5MB.');
    }
    
    // Generar nombre único para el archivo
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = generateUniqueFilename($category, $file['name'], $uploadDir);
    $uploadPath = $uploadDir . $filename;
    
    // Crear directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Collision detection loop con máximo de intentos
    $attempts = 0;
    $maxAttempts = 100;
    
    while (file_exists($uploadPath) && $attempts < $maxAttempts) {
        $attempts++;
        $filename = generateUniqueFilename($category, $file['name'], $uploadDir);
        $uploadPath = $uploadDir . $filename;
    }
    
    if ($attempts >= $maxAttempts) {
        throw new Exception('No se pudo generar un nombre único para el archivo después de ' . $maxAttempts . ' intentos');
    }
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Error al subir la imagen');
    }
    
    // Log de la imagen subida para auditoria
    $logEntry = date('Y-m-d H:i:s') . " - Imagen subida: $filename (Categoría: $category, Tamaño: " . round($file['size']/1024, 2) . "KB, Intentos: $attempts)\n";
    file_put_contents(__DIR__ . '/../../images/upload_log.txt', $logEntry, FILE_APPEND);
    
    return $filename;
}

function generateUniqueFilename($category, $originalName, $uploadDir) {
    $pathInfo = pathinfo($originalName);
    $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : 'jpg';
    
    // Asegurar que la extensión sea válida
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $validExtensions)) {
        $extension = 'jpg';
    }
    
    // Prefijo base según categoría
    $categoryPrefix = match($category) {
        'figura_aroma' => 'figura_aroma',
        'velas_vidrio' => 'velas_vidrio', 
        'velas_yeso' => 'velas_yeso',
        'velas_figuras' => 'velas_figuras',
        'dia_de_muertos' => 'dia_de_muertos',
        'navidad' => 'navidad',
        'eventos' => 'eventos',
        default => 'producto'
    };
    
    // Generar timestamp con segundos: YYYYMMDD_HHMMSS
    $timestamp = date('Ymd_His');
    
    // Nombre base con formato: categoria_fechaconsegundos.extension
    $baseFilename = $categoryPrefix . '_' . $timestamp . '.' . $extension;
    
    // Si el archivo no existe, usar el nombre base
    if (!file_exists($uploadDir . '/' . $baseFilename)) {
        return $baseFilename;
    }
    
    // Si existe, agregar microsegundos para unicidad
    $microtime = substr(microtime(), 2, 6); // 6 dígitos de microsegundos
    $filename = $categoryPrefix . '_' . $timestamp . '_' . $microtime . '.' . $extension;
    
    // Fallback final con uniqid si aún existe
    if (file_exists($uploadDir . '/' . $filename)) {
        $unique = substr(uniqid(), -6);
        $filename = $categoryPrefix . '_' . $timestamp . '_' . $unique . '.' . $extension;
    }
    
    return $filename;
}

try {
    require_once __DIR__ . '/../mysql_config.php';
    
    // Verificar método
    if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del producto (puede ser JSON o FormData)
    $data = [];
    
    if ($_SERVER['CONTENT_TYPE'] && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        // Datos JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    } else {
        // Datos de formulario
        $data = array_merge($_POST, $_GET);
    }
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('ID del producto requerido');
    }
    
    $productId = intval($data['id']);
    
    // Construir campos a actualizar
    $updateFields = [];
    $params = ['id' => $productId];
    
    $allowedFields = ['name', 'description', 'mayoreo', 'menudeo', 'largo', 'alto', 'ancho', 'category', 'url'];
    
    // Manejar imagen si se subió una nueva
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {
            $imageUrl = handleImageUpload($_FILES['image'], $data['category'] ?? 'producto');
        } catch (Exception $e) {
            throw new Exception('Error al subir imagen: ' . $e->getMessage());
        }
    }
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateFields[] = "$field = :$field";
            $params[$field] = $data[$field];
        }
    }
    
    // Si se subió nueva imagen, actualizar URL
    if ($imageUrl) {
        $updateFields[] = "url = :url";
        $params['url'] = $imageUrl;
    }
    
    if (empty($updateFields)) {
        throw new Exception('No hay campos para actualizar');
    }
    
    // Ejecutar actualización
    $sql = "UPDATE list_product SET " . implode(', ', $updateFields) . " WHERE id = :id AND (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if (!$result) {
        throw new Exception('Error al actualizar el producto');
    }
    
    $affectedRows = $stmt->rowCount();
    
    if ($affectedRows === 0) {
        throw new Exception('Producto no encontrado o no se realizaron cambios');  
    }
    
    // Obtener el producto actualizado
    $stmt = $pdo->prepare("SELECT * FROM list_product WHERE id = :id AND (deleted_at IS NULL OR deleted_at = '' OR deleted_at = ' ')");
    $stmt->execute(['id' => $productId]);
    $updatedProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado exitosamente',
        'product' => $updatedProduct,
        'affected_rows' => $affectedRows
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'received_data' => $data ?? null,
            'file' => __FILE__,
            'line' => __LINE__
        ]
    ]);
}
?>