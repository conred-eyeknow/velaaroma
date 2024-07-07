<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Velas - Día de muertos</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="general/general.css">
</head>
<body>

<?php include_once "./general/header.php"; ?>

<header>
    <h1>Figuras Día de Muertos</h1>
</header>

<div class="container">
    <section class="products" id="productContainer">
</div>

<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


<?php include_once "./general/cookies.php"; ?>

<script> getProductsByCategory("dia_de_muertos"); </script>

</body>
</html>

