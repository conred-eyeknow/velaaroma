<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../mysql_config.php';
    
    // Verificar que es una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Validar datos requeridos
    if (empty($_POST['name']) || empty($_POST['category'])) {
        throw new Exception('El nombre y la categoría son obligatorios');
    }
    
    $uploadedImageUrl = null;
    
    // Manejar subida de imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedImageUrl = handleImageUpload($_FILES['image'], $_POST['category']);
    }
    
    // Preparar datos del producto
    $productData = [
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description'] ?? ''),
        'mayoreo' => floatval($_POST['mayoreo'] ?? 0),
        'menudeo' => floatval($_POST['menudeo'] ?? 0),
        'largo' => floatval($_POST['largo'] ?? 0),
        'alto' => floatval($_POST['alto'] ?? 0),
        'ancho' => floatval($_POST['ancho'] ?? 0),
        'category' => trim($_POST['category']),
        'url' => $uploadedImageUrl ?: generateImageUrl($_POST['category'], $_POST['name'])
    ];
    
    // Insertar en la base de datos (tabla list_product)
    $stmt = $pdo->prepare("
        INSERT INTO list_product (name, description, mayoreo, menudeo, largo, alto, ancho, category, url, created_at, deleted_at) 
        VALUES (:name, :description, :mayoreo, :menudeo, :largo, :alto, :ancho, :category, :url, NOW(), NULL)
    ");
    
    $result = $stmt->execute($productData);
    
    if (!$result) {
        throw new Exception('Error al guardar el producto en la base de datos');
    }
    
    // Obtener el ID del producto creado
    $productId = $pdo->lastInsertId();
    
    // Obtener el producto completo para la respuesta
    $stmt = $pdo->prepare("SELECT * FROM list_product WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado exitosamente',
        'product' => $newProduct,
        'uploaded_image' => $uploadedImageUrl
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Maneja la subida de archivos de imagen
 */
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
    
    // Validación adicional: si el archivo ya existe, generar uno nuevo
    $attempts = 0;
    $maxAttempts = 100;
    while (file_exists($uploadPath) && $attempts < $maxAttempts) {
        $filename = generateUniqueFilename($category, $extension);
        $uploadPath = $uploadDir . $filename;
        $attempts++;
    }
    
    if (file_exists($uploadPath)) {
        throw new Exception('No se pudo generar un nombre único para la imagen después de ' . $maxAttempts . ' intentos');
    }
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Error al subir la imagen');
    }
    
    // Log de la imagen subida para auditoria
    $logEntry = date('Y-m-d H:i:s') . " - Imagen subida: $filename (Categoría: $category, Tamaño: " . round($file['size']/1024, 2) . "KB, Intentos: $attempts)\n";
    file_put_contents(__DIR__ . '/../../images/upload_log.txt', $logEntry, FILE_APPEND);
    
    // Redimensionar imagen si es necesario
    resizeImage($uploadPath, 800, 600);
    
    return $filename;
}

/**
 * Genera un nombre único para la imagen basado en la categoría
 */
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
}/**
 * Redimensiona una imagen manteniendo la proporción
 */
function resizeImage($imagePath, $maxWidth, $maxHeight) {
    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) return false;
    
    list($originalWidth, $originalHeight, $imageType) = $imageInfo;
    
    // Si la imagen ya es pequeña, no hacer nada
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        return true;
    }
    
    // Calcular nuevas dimensiones manteniendo proporción
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = intval($originalWidth * $ratio);
    $newHeight = intval($originalHeight * $ratio);
    
    // Crear imagen desde archivo
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($imagePath);
            break;
        default:
            return false;
    }
    
    // Crear nueva imagen redimensionada
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparencia para PNG y GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
    }
    
    // Redimensionar
    imagecopyresampled(
        $resizedImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $originalWidth, $originalHeight
    );
    
    // Guardar imagen redimensionada
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($resizedImage, $imagePath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($resizedImage, $imagePath, 6);
            break;
        case IMAGETYPE_GIF:
            imagegif($resizedImage, $imagePath);
            break;
    }
    
    // Limpiar memoria
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);
    
    return true;
}

/**
 * Genera una URL de imagen por defecto basada en la categoría
 */
function generateImageUrl($category, $productName) {
    // Buscar una imagen existente similar en la categoría
    $prefix = '';
    
    switch (strtolower($category)) {
        case 'figura_aroma':
            $prefix = 'figura_aroma_';
            break;
        case 'navidad':
            $prefix = 'navidad_';
            break;
        case 'dia_de_muertos':
            $prefix = 'dia_de_muertos_';
            break;
        case 'velas_vidrio':
            $prefix = 'velas_vidrio_';
            break;
        case 'velas_yeso':
            $prefix = 'velas_yeso_';
            break;
        default:
            return 'default_product.jpg';
    }
    
    // Buscar archivos existentes de esta categoría
    $pattern = '../images/' . $prefix . '*';
    $existingFiles = glob($pattern);
    
    if (!empty($existingFiles)) {
        // Retornar el primer archivo encontrado como placeholder
        return basename($existingFiles[0]);
    }
    
    return 'default_product.jpg';
}
?>