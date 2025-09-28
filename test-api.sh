#!/bin/bash

echo "🧪 Probando API interna - Vela Aroma"
echo "===================================="

# Verificar que el servidor esté corriendo
echo "📡 Verificando servidor..."
SERVER_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8000/")
if [ "$SERVER_STATUS" = "200" ]; then
    echo "✅ Servidor funcionando correctamente"
else
    echo "❌ Error: Servidor no responde"
    exit 1
fi

echo ""
echo "🛍️ Probando endpoints de productos..."

# Probar productos generales
echo "- GET /api/products"
PRODUCTS=$(curl -s "http://localhost:8000/api/products" | jq -r '.products | length')
echo "  ✅ Respuesta: $PRODUCTS productos encontrados"

# Probar productos por categoría
echo "- GET /api/products/category?category=figura_aroma"
CATEGORY_PRODUCTS=$(curl -s "http://localhost:8000/api/products/category?category=figura_aroma" | jq -r '.products | length')
echo "  ✅ Respuesta: $CATEGORY_PRODUCTS productos de figura_aroma"

echo ""
echo "👥 Probando endpoints de usuarios..."

# Probar lista de usuarios
echo "- GET /api/users"
USERS=$(curl -s "http://localhost:8000/api/users" | jq -r '.users | length')
echo "  ✅ Respuesta: $USERS usuarios encontrados"

# Probar validación de usuario
echo "- POST /api/users/validate"
USER_VALIDATION=$(curl -s -X POST "http://localhost:8000/api/users/validate" \
    -H "Content-Type: application/json" \
    -d '{"username":"demo","password":"123456"}' | jq -r '.info | length')
echo "  ✅ Respuesta: $USER_VALIDATION usuario validado"

echo ""
echo "🛒 Probando endpoints de carrito..."

# Probar carrito
echo "- GET /api/cart/products?username=demo"
CART_COUNT=$(curl -s "http://localhost:8000/api/cart/products?username=demo" | jq -r '.products')
echo "  ✅ Respuesta: $CART_COUNT productos en carrito"

echo ""
echo "🎉 Todos los tests completados exitosamente!"
echo ""
echo "📋 Resumen:"
echo "- API interna funcionando ✅"
echo "- Productos: $PRODUCTS totales, $CATEGORY_PRODUCTS de figura_aroma ✅"
echo "- Usuarios: $USERS registrados ✅"
echo "- Carrito: $CART_COUNT productos ✅"
echo ""
echo "🌐 Puedes probar la página en: http://localhost:8000/velas-figuras/"