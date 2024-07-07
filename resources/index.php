<?php

    header("Content-Type: text/html;charset=utf-8");

    //Se agregan las variables de sesiones
    session_start();
    include __DIR__.'/../sesiones.php';

?>

<!DOCTYPE html>
<html lang="en">
<head> 
		 <title>Eyeknow| Home </title>
         <?php  echo $_head; ?>
</head>




  <body class="body-404">

    <div class="error-head"> </div>

    <div class="container ">

      <section class="error-wrapper text-center">
          <h1><img src="images/404.png" alt=""></h1>
          <div class="error-desk">
              <h2>page not found</h2>
              <p class="nrml-txt">We Couldn’t Find This Page</p>
          </div>
          <a href="../index.html" class="back-btn"><i class="fa fa-home"></i> Back To Home</a>
      </section>

    </div>


  </body>
</html>