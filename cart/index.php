<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma - Carrito de compras</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!--Prod --> 
    <script src="https://www.paypal.com/sdk/js?client-id=AUpJx1Jl_sAaZSizdW5G2ey_7cYkr7W24cNlz2uvNPr6PgcuTob7QeRwFB6zKojLzmt4rhaYkXH83Kqg&currency=MXN" data-sdk-integration-source="button-factory"></script>
    <link rel="stylesheet" href="./../general/general.css">
    <link rel="stylesheet" href="./../general/cart.css">
</head>
<body>

<?php include_once "./../general/header.php"; ?>

<header>
    <h1>Carrito de compras</h1>
</header>

<div class="container">
    <div class="product-container">
        <div id="productTableContainer"></div>
    </div>
    <div class="summary-container">
        <p id="total-quantity"></p>
        <p id="total-envio"></p>
        <p id="total-products"></p>

        <!-- Línea divisoria personalizada -->
        <div class="divider"></div>

        <p id="total-value"></p>
        <button id="proceed-to-payment">Proceder al pago</button>
        <div id="paypal-container" style="display: none;">
            <div id="paypal-button-container"></div>
        </div>
    </div>
</div>

<!-- Modal de Sugerencia de WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whatsappModalLabel">Sugerencia para tu compra</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Para evitar el costo de envío, te sugerimos realizar tu compra a través de WhatsApp. Podemos acordar un punto medio en alguna estación del metro para entregarte el producto sin costo adicional.</p>
        <p><strong>Escríbenos en WhatsApp:</strong> <a href="https://wa.me/5215548611076?text=Hola, estoy interesado en realizar una compra." target="_blank" class="btn btn-success">Enviar mensaje por WhatsApp</a></p>
        <p>Si prefieres continuar con tu compra y pagar el envío, puedes proceder.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="showPayPal()">Pagar con tarjeta o paypal</button>
      </div>
    </div>
  </div>
</div>

<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<?php include_once "./../general/cookies.php"; ?>

<script>
    let cartProducts = []; // Variable para almacenar los productos del carrito

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
                cartProducts = response.products; // Guardamos los productos en la variable global
                populateTable(cartProducts);
                updateTotals(cartProducts);

                // Muestra el modal de sugerencia de WhatsApp al hacer clic
                document.getElementById('proceed-to-payment').addEventListener('click', function() {
                    $('.modal').modal('hide'); // Cerrar otros modales antes
                    $('#whatsappModal').modal('show'); // Mostrar el modal de WhatsApp
                });
            }
        });
    }

    function showPayPal() {
        // Ocultar el modal de WhatsApp y mostrar el contenedor de PayPal
        $('#whatsappModal').modal('hide');
        document.getElementById('paypal-container').style.display = 'block';
        initializePayPalButton(cartProducts); // Pasamos los productos a la función
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
        let totalValue = products.reduce((acc, product) => acc + (parseFloat(product.valor) || 0), 0) + 150;
        let productos = products.reduce((acc, product) => acc + (parseFloat(product.valor) || 0), 0);
        let totalQuantity = products.reduce((acc, product) => acc + (parseInt(product.cantidad) || 0), 0);

        document.getElementById('total-quantity').innerText = `Número de productos: ${totalQuantity}`;
        document.getElementById('total-envio').innerText = `Envio: $150.00`;
        document.getElementById('total-products').innerText = `Total Productos: ${productos}`;
        document.getElementById('total-value').innerText = `Total incluyendo envio: ${formatPrice(totalValue)}`;
    }

    function initializePayPalButton(products) {
        // Calcular el valor total de los productos
        let totalValue = products.reduce((acc, product) => acc + (parseFloat(product.valor) || 0), 0);

        // Agregar 150 pesos al total
        totalValue += 150;

        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: totalValue.toFixed(2) // Valor total con los 150 pesos extra
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    callApiAfterPayment();
                });
            },
            onError: function(err) {
                console.error(err);
                alert('Ocurrió un error al procesar el pago.');
            }
        }).render('#paypal-button-container');
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

<style>
.divider {
    border-top: 1px solid #ccc; /* Define el estilo de la línea */
    margin: 10px 0; /* Define el espacio alrededor de la línea */
}

/* Asegurar que el modal esté por encima de otros elementos */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

body.modal-open {
    overflow: hidden; /* Evitar el scroll en la página mientras el modal está abierto */
}
</style>
