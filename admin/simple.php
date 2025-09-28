<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Admin - Vela Aroma</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../general/general.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .admin-header h1 {
            color: #8B4513;
            margin: 0;
            font-size: 28px;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #8B4513, #D2691E);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(139, 69, 19, 0.3);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #8B4513, #D2691E);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .close {
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
            color: white;
        }
        
        .close:hover {
            color: #FFE4B5;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #8B4513;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B4513;
        }
        
        .image-upload {
            border: 3px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .image-upload:hover {
            border-color: #8B4513;
        }
        
        .image-upload input[type="file"] {
            display: none;
        }
        
        .image-preview {
            margin-top: 15px;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #228B22, #32CD32);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #1F7A1F, #2EBF2E);
        }
        
        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        /* Tabla de productos */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .products-table th {
            background-color: #8B4513;
            color: white;
            font-weight: bold;
        }
        
        .products-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

<?php include_once "../general/header.php"; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>🛍️ Panel de Administración - Vela Aroma</h1>
        <button onclick="showModal()" class="btn-add">➕ Agregar Nuevo Producto</button>
    </div>

    <!-- Lista de productos -->
    <div id="productsContainer">
        <p>⏳ Cargando productos...</p>
    </div>
</div>

<!-- Modal para agregar producto -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>📦 Agregar Nuevo Producto</h2>
            <button class="close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="productForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">📝 Nombre del Producto *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">🏷️ Categoría *</label>
                        <select id="category" name="category" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="figura_aroma">🎭 Figura Aroma</option>
                            <option value="velas_vidrio">🫙 Velas Vidrio</option>
                            <option value="vela_yeso">🕯️ Vela Yeso</option>
                            <option value="dia_de_muertos">💀 Día de Muertos</option>
                            <option value="navidad">🎄 Navidad</option>
                            <option value="eventos">🎉 Eventos</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="mayoreo">💰 Precio Mayoreo</label>
                        <input type="number" id="mayoreo" name="mayoreo" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="menudeo">🏪 Precio Menudeo</label>
                        <input type="number" id="menudeo" name="menudeo" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="largo">📏 Largo (cm)</label>
                        <input type="number" id="largo" name="largo" step="0.1" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="alto">📐 Alto (cm)</label>
                        <input type="number" id="alto" name="alto" step="0.1" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ancho">📊 Ancho (cm)</label>
                        <input type="number" id="ancho" name="ancho" step="0.1" min="0">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="description">📄 Descripción</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>🌅 Imagen del Producto</label>
                        <div class="image-upload" onclick="document.getElementById('image').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #8B4513;"></i>
                            <p>Haz clic para seleccionar una imagen</p>
                            <p style="font-size: 14px; color: #666;">JPG, PNG o GIF - Máximo 5MB</p>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">💾 Guardar Producto</button>
            </form>
        </div>
    </div>
</div>

<script>
// === VARIABLES GLOBALES ===
let products = [];

// === FUNCIONES DEL MODAL ===
function showModal() {
    document.getElementById('productModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('productForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeModal();
    }
}

// === PREVIEW DE IMAGEN ===
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
});

// === ENVÍO DEL FORMULARIO ===
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    // Mostrar estado de carga
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '⏳ Guardando...';
    submitBtn.disabled = true;
    
    // Enviar datos
    fetch('/api/products/create-with-image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Producto agregado exitosamente!');
            closeModal();
            loadProducts(); // Recargar la lista
        } else {
            alert('❌ Error: ' + (data.error || 'No se pudo agregar el producto'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión. Intenta de nuevo.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// === CARGAR PRODUCTOS ===
function loadProducts() {
    fetch('/api/products/index.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                products = data.products;
                renderProducts();
            } else {
                document.getElementById('productsContainer').innerHTML = 
                    '<p style="color: red;">❌ Error al cargar productos: ' + data.error + '</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('productsContainer').innerHTML = 
                '<p style="color: red;">❌ Error de conexión al cargar productos</p>';
        });
}

// === RENDERIZAR PRODUCTOS ===
function renderProducts() {
    if (products.length === 0) {
        document.getElementById('productsContainer').innerHTML = 
            '<p>📦 No hay productos registrados aún. ¡Agrega el primero!</p>';
        return;
    }
    
    let html = `
        <h2>📋 Productos Registrados (${products.length})</h2>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Mayoreo</th>
                    <th>Menudeo</th>
                    <th>Dimensiones</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    products.forEach(product => {
        const imageUrl = product.url || '/images/default_product.jpg';
        const dimensions = `${product.largo || 0} × ${product.alto || 0} × ${product.ancho || 0} cm`;
        
        html += `
            <tr>
                <td><img src="${imageUrl}" alt="${product.name}" class="product-image"></td>
                <td><strong>${product.name}</strong></td>
                <td><span style="background: #8B4513; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">${product.category}</span></td>
                <td>$${parseFloat(product.mayoreo || 0).toFixed(2)}</td>
                <td>$${parseFloat(product.menudeo || 0).toFixed(2)}</td>
                <td><small>${dimensions}</small></td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    document.getElementById('productsContainer').innerHTML = html;
}

// === INICIALIZACIÓN ===
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
});
</script>

</body>
</html>