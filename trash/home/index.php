<?php 

include "./../../eyeknow-config-web/init.php";


##-- Page variables
$__Company__ = "Visor Financiero";
$__PageTitle__ = "Comentarios";

// *****************************************************************************************************************
// ************************************************* Page config ***************************************************


?> 
 
<!-- Inicia header de página  --> 
<!-- Formato: horizontal -->
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title><?php echo $__Company__; ?> - <?php echo $__PageTitle__; ?>  </title>
    
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <?php include_once "../css.php"; ?> 

</head>
<body class="full-width"> 

<!--<img src="http://portal.visorfinanciero.info/images/visor_financiero.jpg" style="width: 10%; margin-right: 5%; ; margin-left: 5%;">-->

<a href="http://portal.visorfinanciero.info/analisys/" class="redondo">Análisis vista</a>

<a href="http://portal.visorfinanciero.info/comments/" class="redondo">Comentarios vista</a>

<a href="http://portal.visorfinanciero.info/analisys/admin.php" class="redondo">Análisis configuración</a>

<a href="http://portal.visorfinanciero.info/comments/admin.php" class="redondo">Comentarios configuración</a>


<!--main content start-->
<section id="main-content">
    <section class="wrapper">
	   


        
    <!--main content end-->
    </section>
</section>
<!-- Placed js at the end of the document so the pages load faster -->
 
    <?php include_once "../js.php"; ?> 

</body>
</html> 

