<?php
require_once __DIR__ . '/mysql_config.php';

try {
    echo "=== CORRECCIÓN AUTOMÁTICA DE URLs DE IMÁGENES ===\n\n";
    
    // Obtener todos los productos con URLs que podrían necesitar corrección
    $sql = "SELECT id, name, url, category FROM list_product WHERE url IS NOT NULL AND url != ''";
    $products = MySQLDB::fetchAll($sql);
    
    echo "Productos encontrados: " . count($products) . "\n\n";
    
    $corrected = 0;
    $alreadyCorrect = 0;
    $notFound = 0;
    
    foreach ($products as $product) {
        $originalUrl = trim($product->url);
        $correctedUrl = validateAndFixImageUrl($originalUrl, $product->category);
        
        if ($originalUrl !== $correctedUrl) {
            // Actualizar en la base de datos
            $updateSql = "UPDATE list_product SET url = ? WHERE id = ?";
            MySQLDB::execute($updateSql, [$correctedUrl, $product->id]);
            
            echo "✅ CORREGIDO - ID: {$product->id}, Nombre: {$product->name}\n";
            echo "   Antes: {$originalUrl}\n";
            echo "   Después: {$correctedUrl}\n\n";
            $corrected++;
        } else {
            if ($correctedUrl === '/images/default_product.jpg') {
                echo "⚠️  SIN IMAGEN - ID: {$product->id}, Nombre: {$product->name}\n";
                echo "   URL: {$originalUrl}\n\n";
                $notFound++;
            } else {
                $alreadyCorrect++;
            }
        }
    }
    
    echo "=== RESUMEN ===\n";
    echo "✅ URLs corregidas: {$corrected}\n";
    echo "✓ URLs ya correctas: {$alreadyCorrect}\n";
    echo "⚠️  Imágenes no encontradas: {$notFound}\n";
    echo "📊 Total procesados: " . count($products) . "\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

/**
 * Función copiada del endpoint para validar URLs
 */
function validateAndFixImageUrl($url, $category) {
    // Limpiar la URL de espacios y saltos de línea
    $url = trim($url);
    
    // Si ya es una URL completa (http/https), mantenerla
    if ($url && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
        return $url;
    }
    
    // Si está vacía, usar imagen por defecto
    if (empty($url)) {
        return '/images/default_product.jpg';
    }
    
    // Asegurar que tenga el prefijo /images/
    if (!str_starts_with($url, '/images/')) {
        if (str_starts_with($url, '/')) {
            $url = '/images' . $url;
        } else {
            $url = '/images/' . $url;
        }
    }
    
    // Verificar si el archivo existe físicamente
    $imagePath = __DIR__ . '/../' . ltrim($url, '/');
    
    if (file_exists($imagePath)) {
        return $url;
    }
    
    // Si no existe, intentar variaciones comunes
    $filename = basename($url);
    $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    $possibleExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    foreach ($possibleExtensions as $ext) {
        $testPath = __DIR__ . '/../images/' . $nameWithoutExt . '.' . $ext;
        if (file_exists($testPath)) {
            return '/images/' . $nameWithoutExt . '.' . $ext;
        }
    }
    
    // Intentar con "figuras" en lugar de "figura" si es figura_aroma
    if ($category === 'figura_aroma' && strpos($filename, 'figura_aroma') !== false) {
        $fixedFilename = str_replace('figura_aroma', 'figuras_aroma', $filename);
        $testPath = __DIR__ . '/../images/' . $fixedFilename;
        if (file_exists($testPath)) {
            return '/images/' . $fixedFilename;
        }
        
        // También probar con diferentes extensiones
        $nameWithoutExt = pathinfo($fixedFilename, PATHINFO_FILENAME);
        foreach ($possibleExtensions as $ext) {
            $testPath = __DIR__ . '/../images/' . $nameWithoutExt . '.' . $ext;
            if (file_exists($testPath)) {
                return '/images/' . $nameWithoutExt . '.' . $ext;
            }
        }
    }
    
    // Si no se encuentra nada, usar imagen por defecto
    return '/images/default_product.jpg';
}
?>