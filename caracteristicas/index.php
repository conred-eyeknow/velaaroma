<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma - Carrito de Compras</title>    
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

<!-- Sección para los colores disponibles -->
<div class="mt-5 text-center">
    <h2>Colores Disponibles</h2>
    <div>
        <div class="color-text">Blanco</div> 
        <div class="color-text">Negro</div>
        <div class="color-text">Rojo</div>
        <div class="color-text">Azul</div>
        <div class="color-text">Verde</div>
    </div>
</div>

<!-- Sección para los aromas disponibles -->
<div class="mt-5 text-center">
    <h2>Aromas Disponibles</h2>
    <ul class="list-unstyled">
        <li>Lavanda</li>
        <li>Vainilla</li>
        <li>Canela</li>
        <li>Naranja Vainilla</li>
        <li>Maderas</li>
        <li>Citronela</li>
        <li>Frutos Rojos</li>
        <li>Romero Verbena</li>
        <li>Manzana-Canela</li>
        <li>Bambú y Flor de Naranjo</li>
        <li>Menta</li>
        <li>Orquídea</li>
        <li>Manzanilla</li>
        <li>Mandarina</li>
        <li>Cedro</li>
        <li>Violeta</li>
        <li>Ciprés</li>
        <li>Limón</li>
        <li>Sándalo</li>
        <li>Cempasúchil</li>
        <li>Pan de Muerto</li>
        <li>Coco</li>
        <li>Bosque Navideño</li>
        <li>Bastón de Caramelo</li>
        <li>Galleta de Jengibre</li>
        <li>Pino Navideño</li>
        <li>Flor de Nochebuena</li>
        <li>Chocolate y Caramelo</li>
        <li>Café</li>
        <li>Tutti Frutti</li>
        <li>Cereza</li>
        <li>Maderas Mediterráneas</li>
    </ul>
</div>

<footer class="text-center">
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<?php include_once "./../general/cookies.php"; ?>

<script>

</script>

<style>
    .divider {
        border-top: 1px solid #ccc; /* Define el estilo de la línea */
        margin: 10px 0; /* Define el espacio alrededor de la línea */
    }
    .color-text {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
    }
</style>

</body>
</html>
