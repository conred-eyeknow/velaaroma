# 🚀 INSTRUCCIONES DE DESPLIEGUE EN PRODUCCIÓN

## ✅ ESTADO ACTUAL
- ✅ **Archivos de configuración incluidos en git**
- ✅ **API completa con datos reales**
- ✅ **155 productos funcionando**
- ✅ **Base de datos conectada: `82.197.82.28`**

## 📋 PASOS PARA PRODUCCIÓN

### 1. **Descargar código actualizado**
```bash
git pull origin master
```

### 2. **Verificar archivos de configuración**
Los siguientes archivos YA están incluidos en el repositorio:
- ✅ `api/mysql_config.php` - Configuración principal
- ✅ `api/mysql_config_real.php` - Configuración avanzada
- ✅ `api/index.php` - Router de API
- ✅ `api/controllers/` - Todos los controladores

### 3. **Verificar credenciales en producción**
El archivo `api/mysql_config.php` contiene:
```php
define('DB_HOST', '82.197.82.28');
define('DB_NAME', 'u783538349_velaaroma');
define('DB_USER', 'u783538349_velaaroma');
define('DB_PASS', 'v3L44r0m4#');
```

### 4. **Probar endpoints**
- `/api/products` - Todos los productos
- `/api/products/category?category=figura_aroma` - Productos por categoría
- `/api/users` - Gestión de usuarios
- `/api/cart` - Gestión de carrito

## 🔧 TROUBLESHOOTING

### Error: "No such file or directory mysql_config.php"
**Solución**: Los archivos ya están en git, hacer `git pull origin master`

### Error: "Access denied for user"
**Solución**: Verificar que la IP del servidor esté autorizada en MySQL

### Error: "Table doesn't exist"
**Solución**: Las tablas correctas son:
- `list_product` (155 productos)
- `users` 
- `va_cart`
- `va_address`

## 📊 DATOS EN PRODUCCIÓN

### Productos Reales Disponibles:
- **56 productos** categoria `figura_aroma`
- **27 productos** categoria `navidad`
- **17 productos** categoria `velas_vidrio`
- **16 productos** categoria `vela_yeso`
- **7 productos** categoria `dia_de_muertos`
- **Más categorías...**

### Estructura de Base de Datos:
```sql
-- Tabla principal de productos
list_product (155 registros)
├── id, name, description
├── mayoreo, menudeo (precios)
├── category, url (imagen)
└── created_at, deleted_at

-- Tablas de usuarios y carrito
users (lista para usar)
va_cart (lista para usar) 
va_address (lista para usar)
```

## 🎯 VERIFICACIÓN FINAL

1. **Probar página velas-figuras**: Debe mostrar productos reales
2. **Verificar imágenes**: Deben cargar desde `/images/`
3. **Confirmar datos**: NO debe haber datos dummy/simulados
4. **API Response**: Debe indicar `"data_source": "REAL_DATABASE"`

---
**¡Todo listo para producción con datos reales!** 🚀

*Última actualización: 2025-09-28*