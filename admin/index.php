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
    <button onclick="addNewRow()">Agregar Nueva Fila</button>
    <div id="productTableContainer"></div>
</div>
<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>
<?php include_once "./../js/js.php"; ?>
<?php include_once "./../general/cookies.php"; ?>
<script> 
getProductsCart(); 

function getProductsCart() {
    $.ajax({
        url: 'https://api.velaaroma.com/v1/products',
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
        url: 'https://api.velaaroma.com/v1/products/update',
        type: "POST",
        data: 'column=' + column + '&val=' + $(editableObj).text() + '&id=' + id,
        success: function(r){
            $(editableObj).css("background","#FDFDFD");
        }
    });
}

function addNewRow() {
    const table = document.querySelector('#productTableContainer table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td contenteditable="true"></td>
        <td></td>
        <td></td>
        <td><button onclick="saveNewRow(this)">Guardar</button></td>
    `;
    table.appendChild(newRow);
}

function saveNewRow(button) {
    const row = button.closest('tr');
    const product = {
        name: row.cells[1].innerText,
        description: row.cells[2].innerText,
        mayoreo: row.cells[3].innerText,
        menudeo: row.cells[4].innerText,
        largo: row.cells[5].innerText,
        alto: row.cells[6].innerText,
        ancho: row.cells[7].innerText,
        category: row.cells[8].innerText,
        url: row.cells[9].innerText
    };

    $.ajax({
        url: 'https://api.velaaroma.com/v1/products/create',
        type: "POST",
        data: product,
        success: function(response){
            row.cells[0].innerText = response.products[0].id;
            row.cells[10].innerText = response.products[0].created_at;
            button.remove();
        }
    });
}
</script>
</body>
</html>
