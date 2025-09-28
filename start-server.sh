#!/bin/bash

# 🚀 Script de Inicio Automático - Vela Aroma
# Maneja automáticamente puertos y reinicio del servidor

echo "🕯️  Iniciando Vela Aroma Server..."
echo "=================================="

# Directorio del proyecto
PROJECT_DIR="/Users/ibalderas/Documents/webapps/velaaroma"
cd "$PROJECT_DIR"

# Función para limpiar procesos PHP anteriores
cleanup_php_servers() {
    echo "🧹 Limpiando servidores PHP anteriores..."
    pkill -9 -f "php -S" 2>/dev/null || true
    sleep 2
}

# Función para encontrar un puerto disponible
find_available_port() {
    for port in 8004 8005 8006 8007 8008; do
        if ! lsof -i :$port >/dev/null 2>&1; then
            echo $port
            return
        fi
    done
    echo 8009  # Puerto de respaldo
}

# Función para iniciar el servidor
start_server() {
    local port=$1
    echo "🌐 Iniciando servidor en puerto $port..."
    echo "📁 Directorio: $PROJECT_DIR"
    
    # Verificar que estamos en el directorio correcto
    if [ ! -f "index.php" ] || [ ! -d "admin" ] || [ ! -d "api" ]; then
        echo "❌ Error: No se encuentra la estructura del proyecto"
        echo "   Verifique que esté en el directorio correcto"
        exit 1
    fi
    
    echo "✅ Estructura del proyecto verificada"
    echo ""
    echo "🚀 Servidor iniciado en: http://localhost:$port"
    echo "📋 URLs disponibles:"
    echo "   • Admin Panel: http://localhost:$port/admin/"
    echo "   • Página Principal: http://localhost:$port/"
    echo "   • Velas Figuras: http://localhost:$port/velas-figuras/"
    echo "   • API Productos: http://localhost:$port/api/products/index.php"
    echo ""
    echo "⚠️  Para detener el servidor, presiona Ctrl+C"
    echo "🔄 Si el servidor se desconecta, ejecuta este script nuevamente"
    echo ""
    
    # Iniciar servidor PHP
    php -S localhost:$port
}

# Función principal
main() {
    cleanup_php_servers
    
    local port=$(find_available_port)
    echo "🔍 Puerto disponible encontrado: $port"
    
    start_server $port
}

# Manejo de señales para limpieza
trap 'echo -e "\n🛑 Deteniendo servidor..."; cleanup_php_servers; exit 0' INT TERM

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP no está instalado o no está en el PATH"
    echo "   Instale PHP con: brew install php"
    exit 1
fi

# Ejecutar función principal
main