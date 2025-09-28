<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./../general/general.css">
</head>
<body>

<?php include_once "./../general/header.php"; ?>

<div class="container">
    <div class="admin-header">
        <h1>🛍️ Panel de Administración - Vela Aroma</h1>
        <button onclick="showAddProductForm()" class="btn-add-product">➕ Agregar Nuevo Producto</button>
    </div>

    <!-- Modal para agregar producto -->
    <div id="addProductModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📦 Agregar Nuevo Producto</h2>
                <span class="close" onclick="closeAddProductModal()">&times;</span>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="productName">📝 Nombre del Producto *</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="productCategory">🏷️ Categoría *</label>
                        <select id="productCategory" name="category" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="figura_aroma">🎭 Figura Aroma</option>
                            <option value="velas_vidrio">🫙 Velas Vidrio</option>
                            <option value="vela_yeso">🕯️ Vela Yeso</option>
                            <option value="dia_de_muertos">💀 Día de Muertos</option>
                            <option value="navidad">🎄 Navidad</option>
                            <option value="eventos">🎉 Eventos</option>
                            <option value="yeso">🏺 Yeso</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="productDescription">📄 Descripción</label>
                        <textarea id="productDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="productMayoreo">💰 Precio Mayoreo *</label>
                        <input type="number" id="productMayoreo" name="mayoreo" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="productMenudeo">💵 Precio Menudeo *</label>
                        <input type="number" id="productMenudeo" name="menudeo" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="productLength">📏 Largo (cm)</label>
                        <input type="number" id="productLength" name="largo" step="0.1" placeholder="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="productHeight">📐 Alto (cm)</label>
                        <input type="number" id="productHeight" name="alto" step="0.1" placeholder="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="productWidth">📊 Ancho (cm)</label>
                        <input type="number" id="productWidth" name="ancho" step="0.1" placeholder="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="productWeight">⚖️ Peso (g)</label>
                        <input type="number" id="productWeight" name="peso" placeholder="0">
                    </div>

                    <div class="form-group full-width">
                        <label for="productImage">🖼️ Imagen del Producto *</label>
                        <div class="image-upload-area">
                            <input type="file" id="productImage" name="image" accept="image/*" required>
                            <div class="upload-preview">
                                <div id="imagePreview" class="image-preview"></div>
                                <div class="upload-text">
                                    <p>📸 Selecciona una imagen</p>
                                    <small>Formatos: JPG, PNG, WebP (máx. 5MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeAddProductModal()" class="btn-cancel">❌ Cancelar</button>
                    <button type="submit" class="btn-save">💾 Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productTableContainer"></div>
</div>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
}

.btn-add-product {
    background: #28a745;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-add-product:hover {
    background: #218838;
    transform: translateY(-2px);
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background-color: #fefefe;
    margin: 2% auto;
    padding: 0;
    border-radius: 15px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.close:hover {
    opacity: 0.7;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
}

.image-upload-area {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: border-color 0.3s ease;
}

.image-upload-area:hover {
    border-color: #667eea;
}

.image-preview {
    max-width: 200px;
    max-height: 200px;
    margin: 0 auto 15px;
    border-radius: 8px;
    overflow: hidden;
    display: none;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    padding: 0 30px 30px;
}

.btn-save, .btn-cancel {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save {
    background: #28a745;
    color: white;
}

.btn-save:hover {
    background: #218838;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #545b62;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>
<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>
<?php include_once "./../js/js.php"; ?>
<?php include_once "./../general/cookies.php"; ?>
<script> 
getProductsCart(); 

function getProductsCart() {
    $.ajax({
        url: '/api/products/index.php',
        type: "GET",
        success: function(response){ 
            populateTable(response.products);
        }
    });
}

function formatPrice(value) {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
}

function generateTableHTML(products) {
    let tableHTML = `
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Mayoreo</th>
                    <th>Menudeo</th>
                    <th>Largo</th>
                    <th>Alto</th>
                    <th>Ancho</th>
                    <th>Peso</th>
                    <th>Categoria</th>
                    <th>Url</th>
                    <th>Creación</th>
                    <th>Eliminación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;

    products.forEach(product => {
        tableHTML += `
            <tr>
                <td>${product.id}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'name',${product.id})">${product.name}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'description',${product.id})">${product.description}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'mayoreo',${product.id})">${product.mayoreo}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'menudeo',${product.id})">${product.menudeo}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'largo',${product.id})">${product.largo}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'alto',${product.id})">${product.alto}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'ancho',${product.id})">${product.ancho}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'peso',${product.id})">${product.peso}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'category',${product.id})">${product.category}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'url',${product.id})">${product.url}</td>
                <td contenteditable="false" onBlur="updateListProduct(this,'created_at',${product.id})">${product.created_at}</td>
                <td contenteditable="true" onBlur="updateListProduct(this,'deleted_at',${product.id})">${product.deleted_at}</td>
                <td><button onclick="deleteListProduct(${product.id})">Eliminar</button></td>
            </tr>
        `;
    });

    return tableHTML;
}

function populateTable(products) {
    const container = document.getElementById('productTableContainer');
    container.innerHTML = generateTableHTML(products);
}

function updateListProduct(editableObj,column,id) {
    $(editableObj).css("background","#ffffff");
    $.ajax({
        url: '/api/products/update.php',
        type: "POST",
        data: 'column=' + column + '&val=' + $(editableObj).text() + '&id=' + id,
        success: function(r){
            $(editableObj).css("background","#FDFDFD");
        }
    });
}

// === FUNCIONES DEL MODAL ===
function showAddProductForm() {
    document.getElementById('addProductModal').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevenir scroll del body
}

function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('addProductForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
}

// === PREVIEW DE IMAGEN ===
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});

// === ENVÍO DEL FORMULARIO ===
document.getElementById('addProductForm').addEventListener('submit', function(e) {
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
            closeAddProductModal();
            getProductsCart(); // Recargar la tabla
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

// === FUNCIONES HEREDADAS (para compatibilidad) ===
function addNewRow() {
    showAddProductForm();
}

function saveNewRow(button) {
    // Esta función ya no se usa, pero la mantenemos por compatibilidad
    showAddProductForm();
}
</script>
</body>
</html>
