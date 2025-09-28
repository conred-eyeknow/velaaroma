# 📚 API INTERNA VELA AROMA

## 🚀 Sistema Simple con PHP + JSON

Tu nueva API interna está diseñada para ser **simple, rápida y fácil de manejar**. No requiere base de datos MySQL, solo PHP y archivos JSON.

---

## 📋 **CARACTERÍSTICAS**

- ✅ **Sin MySQL** - Solo archivos JSON
- ✅ **Fácil backup** - Copia la carpeta `/api/data/`
- ✅ **Sin configuración compleja** - Listo en minutos
- ✅ **Compatible** - Funciona igual que la API externa
- ✅ **Portable** - Lleva tu proyecto a cualquier servidor

---

## 🔧 **INSTALACIÓN RÁPIDA**

### 1. Ejecutar Setup (Una sola vez)
```
http://localhost:8000/setup.php
```

### 2. Probar API
```
http://localhost:8000/api_test.php
```

### 3. ¡Listo! 🎉

---

## 📖 **ENDPOINTS DISPONIBLES**

### 👤 **USUARIOS**

#### Registrar Usuario
```
POST /api/users
{
  "name": "Juan",
  "first_last_name": "Pérez",
  "email": "juan@email.com",
  "username": "juan123",
  "password": "mipassword"
}
```

#### Validar Login
```
POST /api/users/validate
{
  "username": "juan123",
  "password": "mipassword"
}
```

#### Confirmar Email
```
POST /api/users/confirmation
{
  "code": "123456",
  "virtual_address": "juan@email.com"
}
```

#### Recuperar Contraseña
```
POST /api/users/recovery
{
  "email": "juan@email.com"
}
```

### 🕯️ **PRODUCTOS**

#### Listar Productos
```
GET /api/products
```

#### Productos por Categoría
```
GET /api/products/category?category=figura_aroma
```

Categorías disponibles:
- `figura_aroma` - Velas con figuras
- `velas_yeso` - Recipientes de yeso
- `velas_vidrio` - Recipientes de vidrio
- `dia_de_muertos` - Día de muertos
- `navidad` - Navidad
- `eventos` - Eventos

#### Crear Producto
```
POST /api/products/create
{
  "name": "Nueva Vela",
  "category": "figura_aroma",
  "menudeo": 85,
  "mayoreo": 65,
  "alto": "10",
  "ancho": "8",
  "largo": "6"
}
```

### 🛒 **CARRITO**

#### Agregar al Carrito
```
POST /api/cart/products
{
  "username": "juan123",
  "product_id": "figura_1",
  "cantidad": 2,
  "color": "Blanco",
  "aroma": "Lavanda"
}
```

#### Ver Carrito
```
GET /api/cart/products?username=juan123&status=in_progress
```

#### Procesar Venta
```
POST /api/cart/products/sell
{
  "username": "juan123"
}
```

---

## 📁 **ESTRUCTURA DE DATOS**

Los datos se guardan en `/api/data/`:

```
api/data/
├── users.json       # Usuarios registrados
├── products.json    # Catálogo de productos  
├── cart.json        # Items en carritos
├── orders.json      # Órdenes procesadas
├── sessions.json    # Sesiones activas
└── sent_emails.json # Emails enviados (log)
```

---

## 🔧 **ADMINISTRACIÓN**

### Usuario Admin por Defecto
- **Usuario:** `admin`
- **Contraseña:** `admin123`

### Backup Simple
```bash
# Respaldar datos
cp -r api/data/ backup_$(date +%Y%m%d)/

# Restaurar datos  
cp -r backup_20241228/ api/data/
```

### Ver Estadísticas
```
http://localhost:8000/api_test.php
```

---

## 🚀 **MIGRACIÓN A PRODUCCIÓN**

### Subir a tu servidor:
1. Copia toda la carpeta `api/` 
2. Ejecuta `setup.php` una vez
3. Cambia contraseña admin
4. ¡Listo!

### Sin complicaciones:
- No configures MySQL
- No instales nada extra
- Solo PHP (que ya tienes)

---

## 💡 **VENTAJAS**

✅ **Simple** - Fácil de entender y modificar
✅ **Rápido** - Sin overhead de base de datos
✅ **Portable** - Funciona en cualquier hosting con PHP
✅ **Backup fácil** - Solo archivos JSON
✅ **Sin dependencias** - Solo PHP nativo
✅ **Desarrollo ágil** - Cambios inmediatos

---

## 🆘 **SOLUCIÓN DE PROBLEMAS**

### API no responde
1. Verificar que el servidor PHP esté corriendo
2. Revisar permisos de la carpeta `/api/data/`
3. Ejecutar `setup.php` nuevamente

### Datos perdidos
1. Verificar que existe `/api/data/`
2. Restaurar desde backup
3. Ejecutar `setup.php` para recrear

### JavaScript no funciona
1. Abrir consola del navegador (F12)
2. Verificar URLs en `general/cookies.php`
3. Probar con `api_test.php`

---

🎉 **¡Tu API interna está lista y es súper fácil de manejar!**