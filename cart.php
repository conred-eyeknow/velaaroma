<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma - Carrito de compras</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AZT5s4W504aG2GS2GIbRHDuyQ2mDoHWGLulef43Pqsg0XlskKYMfYzCtke_iTyNRz3b9SZzTPigORrPU&currency=MXN" data-sdk-integration-source="button-factory"></script>
    <!--    <script src="https://www.paypal.com/sdk/js?client-id=AUpJx1Jl_sAaZSizdW5G2ey_7cYkr7W24cNlz2uvNPr6PgcuTob7QeRwFB6zKojLzmt4rhaYkXH83Kqg&currency=MXN" data-sdk-integration-source="button-factory"></script>-->
    <link rel="stylesheet" href="./../general/general.css">
    <link rel="stylesheet" href="./../general/cart.css">
</head>
<body>

<?php include_once "./general/header.php"; ?>

<header>
    <h1>Carrito de compras</h1>
</header>

<div class="container">
    <div class="product-container">
        <div id="productTableContainer"></div>
    </div>
    <div class="summary-container">
        <p id="total-quantity"></p>
        <p id="total-value"></p>
        <button id="proceed-to-payment">Proceder al pago</button>
        <!--<button id="proceed-to-sent">Enviate la cotización</button>-->
        <div id="paypal-container" style="display: none;">
            <div id="paypal-button-container"></div>
        </div>
    </div>
</div>

<div id="user-info-form">
    <button class="close-btn" onclick="closeModal()">×</button>
        <br/> <br/>
        <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Correo Electrónico" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" id="address" name="address" placeholder="Dirección" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" id="postal-code" name="postal-code" placeholder="Código Postal" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" id="first-name" name="first-name" placeholder="Nombre" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" id="last-name-pat" name="last-name-pat" placeholder="Apellido Paterno" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" id="last-name-mat" name="last-name-mat" placeholder="Apellido Materno" class="form-control" required>
        </div>
        <button type="submit" onclick="register()" class="btn btn-dark">Enviar</button>
        <br/>
        <div id="result"></div>

</div>

<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<?php include_once "./general/cookies.php"; ?>

<script>
    getProductsCart();

    function getProductsCart() {
        var username = getCookie("username");
        var name = getCookie("name");
        var status = "in_progress";

        $.ajax({
            url: 'https://api.velaaroma.com/v1/cart/products/sell',
            type: "GET",
            data: 'username=' + username + '&status=' + status,
            success: function(response) {
                populateTable(response.products);
                updateTotals(response.products);
                document.getElementById('proceed-to-payment').addEventListener('click', function() {
                    if (name) {
                        document.getElementById('paypal-container').style.display = 'block';
                        initializePayPalButton(response.products);
                    } else {
                        document.getElementById('user-info-form').style.display = 'block';
                    }
                });
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
                        <th>Product</th>
                        <th>Aroma</th>
                        <th>Color</th>
                        <th>Cantidad</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
        `;

        products.forEach(product => {
            let value = product.valor ? parseFloat(product.valor) : 0;
            let quantity = product.cantidad ? parseInt(product.cantidad) : 0;

            tableHTML += `
                <tr>
                    <td data-label="Product">${product.product_id}</td>
                    <td data-label="Aroma">${product.aroma ? product.aroma : 'N/A'}</td>
                    <td data-label="Color">${product.color ? product.color : 'N/A'}</td>
                    <td data-label="Cantidad">${quantity}</td>
                    <td data-label="Valor">${formatPrice(value)}</td>
                    <td data-label="Eliminar"> <button onclick="deleteProductCart(${product.id})">Eliminar</button> </td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        return tableHTML;
    }

    function populateTable(products) {
        const container = document.getElementById('productTableContainer');
        container.innerHTML = generateTableHTML(products);
    }

    function updateTotals(products) {
        let totalValue = products.reduce((acc, product) => acc + (parseFloat(product.valor) || 0), 0);
        let totalQuantity = products.reduce((acc, product) => acc + (parseInt(product.cantidad) || 0), 0);

        document.getElementById('total-quantity').innerText = `Total Cantidad: ${totalQuantity}`;
        document.getElementById('total-value').innerText = `Total Valor: ${formatPrice(totalValue)}`;
    }

    function initializePayPalButton(products) {
        let totalValue = products.reduce((acc, product) => acc + (parseFloat(product.valor) || 0), 0);

        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: totalValue.toFixed(2)
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    //clearCart();
                    callApiAfterPayment();
                });
            },
            onError: function(err) {
                console.error(err);
                alert('Ocurrió un error al procesar el pago.');
            }
        }).render('#paypal-button-container');
    }

    function clearCart() {
        const container = document.getElementById('productTableContainer');
        container.innerHTML = '';
    }

    function callApiAfterPayment() {
        var username = getCookie("username");
        $.ajax({
            url: 'https://api.velaaroma.com/v1/cart/update_status',
            type: "POST",
            data: { username: username },
            success: function(response) {
                getProductsCart();
            }
        });
    }

    function closeModal() {
        document.getElementById('user-info-form').style.display = 'none';
    }

    function register() {
        var name = document.getElementById("first-name").value;
        var first_last_name = document.getElementById("last-name-pat").value;
        var second_last_name = document.getElementById("last-name-mat").value;
        var email = document.getElementById("email").value;
        var password = "";
        var username = getCookie("username");
        var address = document.getElementById("address").value;
        var zipcode = document.getElementById("postal-code").value;
        var telephone = "";
        var subject = "";

        $.ajax({
            url: 'https://api.velaaroma.com/v1/users',
            type: "POST",
            data: 'name=' + name + '&first_last_name=' + first_last_name + '&second_last_name=' 
                + second_last_name + '&email=' + email + '&password=' + password + '&username=' + username 
                + '&zipcode=' + zipcode + '&address=' + address + '&telephone=' + telephone + '&subject=' + subject ,
            success: function(response){ 
            
                if(response.status == 'success'){
                    closeModal();
                    document.getElementById('paypal-container').style.display = 'block';
                    initializePayPalButton(response.products);
                    setCookie("name", response.users[0].name ,30);
                } else {
                    document.getElementById("result").innerHTML = "<p style='color:#000000;font-weight: bold;'>" + response.description + "</p>";
                }
            }
        });
    }

    function deleteProductCart(product_id) {

        var username = getCookie("username");
        var status = "deleted";

        $.ajax({
            url: 'https://api.velaaroma.com/v2/cart/update_status',
            type: "POST",
            data: 'product_id=' + product_id + '&username=' + username + '&status=' + status,
            success: function(response){ 
            
                getProductsCart();
            }
        });
    }
</script>

</body>
</html>
