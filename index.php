<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="./general/general.css">
</head>
<body>

<?php include_once "./general/header.php"; ?>

<div class="cards-container">
    <a href="./figuras-aroma.php" class="card">
        <img src="./../images/figuras_aroma_1.jpg" alt="Velas con Figuras">
        <div class="card-text">Velas con Figuras</div>
    </a>
    <a href="./velas-yeso.php" class="card">
        <img src="./../images/velas_yeso_1.jpg" alt="Velas de yeso">
        <div class="card-text">Velas recipiente de yeso</div>
    </a>
    <a href="./velas-vidrio.php" class="card">
        <img src="./../images/velas_vidrio_1.jpg" alt="Velas de yeso">
        <div class="card-text">Velas recipiente vidrio</div>
    </a>
    <a href="dia-de-muertos.php" class="card">
        <img src="./../images/dia_de_muertos_2.jpg" alt="Día de muertos">
        <div class="card-text">Día de muertos</div>
    </a>
</div>

<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide slide1">
            <div class="slide-text">
                <h2>GRAN VARIEDAD</h2>
                <p>Para todo tipo de eventos y negocios</p>
            </div>
        </div>
        <div class="swiper-slide slide2">
            <div class="slide-text">
                <h2>DISEÑOS ÚNICOS</h2>
                <p>Personaliza tus velas a tu gusto</p>
            </div>
        </div>
        <div class="swiper-slide slide3">
            <div class="slide-text">
                <h2>AROMAS INCREÍBLES</h2>
                <p>Experimenta nuestras fragancias</p>
            </div>
        </div>
    </div>
</div>
</br> </br> </br>
<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 5000, // 5000 ms = 5 seconds
            disableOnInteraction: false,
        },
        effect: 'fade', // Efecto de transición
        fadeEffect: {
            crossFade: true
        },
        speed: 800, // Velocidad de la transición
    });

</script>

<?php include_once "./js/js.php"; ?>
<?php include_once "./general/cookies.php"; ?>

</body>
</html>
