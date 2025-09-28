<?php
/**
 * API Endpoint: /api/products/category
 * Alias para obtener productos por categoría
 * Redirige a index.php con parámetros
 */

// Obtener parámetros
$category = $_GET['category'] ?? null;
$limit = $_GET['limit'] ?? 100;
$offset = $_GET['offset'] ?? 0;

// Construir URL para redirigir a index.php
$queryString = http_build_query([
    'category' => $category,
    'limit' => $limit,
    'offset' => $offset
]);

// Include del endpoint principal
$_GET['category'] = $category;
$_GET['limit'] = $limit;
$_GET['offset'] = $offset;

include __DIR__ . '/index.php';
?>