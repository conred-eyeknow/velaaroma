# 🚀 INICIO RÁPIDO - Vela Aroma Local

## ⚡ Método Más Rápido

### 1. Abrir Terminal
```bash
cd /Users/ibalderas/Documents/webapps/velaaroma
```

### 2. Ejecutar script de inicio
```bash
./start-local.sh
```

### 3. Abrir navegador
```
http://localhost:8000
```

---

## 🔧 Métodos Alternativos

### Método A: PHP Servidor Integrado
```bash
cd /Users/ibalderas/Documents/webapps/velaaroma
php -S localhost:8000
```

### Método B: Con puerto personalizado
```bash
./start-local.sh 3000
# Luego ir a: http://localhost:3000
```

### Método C: Docker (si tienes Docker)
```bash
docker-compose up -d
# Luego ir a: http://localhost:8080
```

---

## 🧪 Verificar que funciona

### ✅ Lista de verificación:
1. **Página principal**: http://localhost:8000/ ✅
2. **Catálogos**: http://localhost:8000/velas-figuras/ ✅  
3. **Login**: http://localhost:8000/login/ ✅
4. **Registro**: http://localhost:8000/register/ ✅
5. **Contacto**: http://localhost:8000/contacto/ ✅

### 🔍 Debugging:
Si algo no funciona, revisa:
- Console del navegador (F12)
- Terminal donde corre el servidor
- Conexión a internet (para API)

---

## 🛑 Para Detener

- **Servidor PHP**: `Ctrl + C` en terminal
- **Docker**: `docker-compose down`

---

## 📝 URLs Importantes

- **Frontend Local**: http://localhost:8000
- **API Producción**: https://api.velaaroma.com/v1/ (sigue funcionando)
- **WhatsApp**: https://wa.me/5215548611076

---

## 💡 Siguientes Pasos

1. ✅ Configurar ambiente local
2. 🔄 Probar todas las funcionalidades  
3. 🛠️ Hacer modificaciones
4. 🚀 Subir cambios a producción

¡Listo para desarrollar! 🎉