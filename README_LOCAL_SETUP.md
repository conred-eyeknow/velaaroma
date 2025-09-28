# 🏠 Configuración Ambiente Local - Vela Aroma

## 📋 Requisitos Previos

### Opción A: XAMPP/MAMP (Recomendado para principiantes)
1. **Descargar XAMPP:** https://www.apachefriends.org/
2. **Descargar MAMP:** https://www.mamp.info/

### Opción B: Servidor PHP integrado (Más simple)
- PHP 7.4+ instalado en tu sistema

### Opción C: Docker (Avanzado)
- Docker Desktop instalado

---

## ⚙️ Configuración Paso a Paso

### 🔧 **Método 1: XAMPP/MAMP**

1. **Instalar XAMPP o MAMP**
2. **Copiar proyecto:**
   ```bash
   # Para XAMPP
   cp -r /Users/ibalderas/Documents/webapps/velaaroma /Applications/XAMPP/htdocs/
   
   # Para MAMP
   cp -r /Users/ibalderas/Documents/webapps/velaaroma /Applications/MAMP/htdocs/
   ```

3. **Iniciar servicios:**
   - Abrir XAMPP/MAMP Control Panel
   - Iniciar Apache
   - (MySQL no es necesario ya que usa API externa)

4. **Acceder:**
   - http://localhost/velaaroma
   - http://localhost:8888/velaaroma (MAMP)

### 🚀 **Método 2: Servidor PHP Integrado (Recomendado)**

1. **Verificar PHP:**
   ```bash
   php --version
   ```

2. **Iniciar servidor:**
   ```bash
   cd /Users/ibalderas/Documents/webapps/velaaroma
   php -S localhost:8000
   ```

3. **Acceder:**
   - http://localhost:8000

### 🐳 **Método 3: Docker**

1. **Crear Dockerfile** (ya incluido en configuración)
2. **Ejecutar:**
   ```bash
   cd /Users/ibalderas/Documents/webapps/velaaroma
   docker build -t velaaroma .
   docker run -p 8080:80 velaaroma
   ```

3. **Acceder:**
   - http://localhost:8080

---

## 🔗 **Configuración de API**

### Producción vs Local
- **Producción:** https://api.velaaroma.com/v1/
- **Local:** Utiliza la misma API de producción (no necesita base de datos local)

### Archivos que consumen API:
- `/login/index.php` - Autenticación
- `/register/index.php` - Registro usuarios
- `/general/cookies.php` - Productos y carrito
- `/admin/index.php` - Gestión productos
- `/cart/index.php` - Carrito compras

---

## 🛠️ **Configuración Adicional**

### Headers CORS (si hay problemas)
Si experimentas problemas de CORS, agrega al inicio de cada archivo PHP:
```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
?>
```

### Certificados SSL Locales (Opcional)
Para HTTPS local:
```bash
# Generar certificado auto-firmado
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365 -nodes
```

---

## 🧪 **Verificación de Funcionamiento**

### ✅ Checklist:
- [ ] Página principal carga correctamente
- [ ] Catálogos muestran productos (requiere conexión a internet)
- [ ] Sistema de login funciona
- [ ] Modal de productos se abre
- [ ] Navegación responsive funciona
- [ ] Imágenes se cargan correctamente

### 🚨 Problemas Comunes:

1. **Error 404:** Verificar que el servidor esté corriendo en el puerto correcto
2. **API no responde:** Verificar conexión a internet
3. **Imágenes no cargan:** Verificar rutas relativas en CSS
4. **JavaScript no funciona:** Verificar console del navegador

---

## 📞 **URLs de Prueba Local**

- **Inicio:** http://localhost:8000/
- **Login:** http://localhost:8000/login/
- **Registro:** http://localhost:8000/register/
- **Velas Figuras:** http://localhost:8000/velas-figuras/
- **Contacto:** http://localhost:8000/contacto/

---

## 💡 **Próximos Pasos**

Una vez funcionando localmente, puedes:
1. Modificar estilos en `/general/general.css`
2. Agregar nuevas funcionalidades
3. Probar cambios antes de subir a producción
4. Desarrollar nuevas características

---

## 🔄 **Sincronización con Producción**

Para mantener sincronizado:
```bash
# Backup antes de cambios
cp -r velaaroma velaaroma_backup_$(date +%Y%m%d)

# Subir cambios a producción (método según tu hosting)
rsync -avz velaaroma/ usuario@servidor:/path/to/production/
```