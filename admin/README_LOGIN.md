# Sistema de Login - Admin Panel Vela Aroma

## 📋 Credenciales de Acceso

**Usuario:** `velaaroma`  
**Contraseña:** `v3L44r0m4#`

## 🔐 Funcionalidades de Seguridad

### Autenticación
- Login con credenciales hardcodeadas
- Verificación en sessionStorage
- Redirección automática si no está autenticado
- Timeout de sesión al cerrar navegador

### Protección
- Verificación JavaScript en cada carga de página
- Botón de logout con confirmación
- Headers de seguridad en .htaccess
- Bloqueo de archivos sensibles

## 🚀 Uso del Sistema

### Para Acceder:
1. Ir a: `http://localhost:8005/admin/login.php`
2. Ingresar credenciales:
   - Usuario: `velaaroma`
   - Contraseña: `v3L44r0m4#`
3. Click en "Iniciar Sesión"

### Panel de Admin:
- URL: `http://localhost:8005/admin/`
- Funciones: Crear, Editar, Eliminar productos
- Logout: Botón "Cerrar Sesión" en el header

## 🛡️ Archivos del Sistema

- `login.php` - Página de inicio de sesión
- `index.php` - Panel principal (protegido)
- `.htaccess` - Configuración de seguridad

## ⚠️ Notas Importantes

- Las credenciales están hardcodeadas en el JavaScript
- La sesión se mantiene solo mientras el navegador esté abierto
- No hay persistencia en base de datos
- Sistema sencillo para uso interno

## 🔄 Flujo de Autenticación

1. **Sin Login** → Redirige a `login.php`
2. **Login Correcto** → Guarda en sessionStorage → Redirige a admin
3. **En Admin** → Verifica sessionStorage → Si no existe → Redirige a login
4. **Logout** → Limpia sessionStorage → Redirige a login