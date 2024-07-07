<?php 

include "./../eyeknow-config-web/init.php";

// *****************************************************************************************************************
// ************************************************* Page config ***************************************************


?> 
<!-- Define que el documento esta bajo el estandar de HTML 5 -->
<!doctype html>

<!-- Representa la raíz de un documento HTML o XHTML. Todos los demás elementos deben ser descendientes de este elemento. -->
<html lang="es">
    
    <head>
        
        <meta charset="utf-8">
        
        <title> Visor Financiero - ¿Olvidate tu contraseña? </title>    
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <meta name="author" content="Visor Financiero">
        <meta name="description" content="Login Visor Financiero">
        <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
        
        <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
        
        <!-- Link hacia el archivo de estilos css -->
        <link rel="stylesheet" href="../css/login.css">

    </head>
    
    <body>
        
        <div id="contenedor">
            <div id="central">
                <div id="login">
                    <div class="titulo">
                        ¿Olvidaste tu contraseña?
                    </div>
                    <form id="loginform">
                        <input type="text" id="email" name="email" placeholder="Correo electrónico" required>
                        
                        <button type="submit" title="Ingresar" name="Ingresar">Recuperar</button>
                    </form>
                </div>
                <div class="inferior">
                    <a href="./../login/">Volver</a>
                </div>
            </div>
        </div>
            
        <?php include_once "../js.php"; ?> 

    </body>
</html>


<script>

    /** Validate credentials */
    function recover_password() {
        
        var user = document.getElementById("email").value;
    
        $.ajax({
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/users/recover_password',
            type: "GET",
            data:'user=' + user,
            success: function(response){ 

                document.getElementById("result").innerHTML = "<p style='color:red;font-weight: bold;'>" + "Se te enviará un correo electrónico para continuar con la recuperación." + "</p>";
                
            }
        });
    }

</script>