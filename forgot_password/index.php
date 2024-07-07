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
        
        <title> Vela Aroma - Recuperar contraseña </title>    
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <meta name="author" content="Vela Aroma">
        <meta name="description" content="Recuperar contraseña Vela Aroma">
        <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
        
        <link rel="icon" type="image/x-icon" href="/../images/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
        
        <!-- Link hacia el archivo de estilos css -->
        <link rel="stylesheet" href="./../general/general.css">

    </head>
    
    <body>
        
        <div id="contenedor">
            <div id="central">
                <div id="login">
                    <div class="titulo">
                        Recupera tu contraseña
                    </div>
                    <div id="loginform">
                        <input type="text" name="email" placeholder="Email" id="email" required>
                        
                        
                        <button type="submit" title="Recuperar" name="Recuperar" onclick="recovery_password()">Recuperar contraseña</button>
                    </div>
                    <div class="pie-form">
                        <a href="./../register/">¿No tienes Cuenta? Registrate</a>
                    </div>
                    <div id="result"></div>
                </div>
                <div class="inferior">
                    <a href="./../login/">Volver</a>
                </div>
            </div>
        </div>
            
        <?php include_once "../js/js.php"; ?> 

    </body>
</html>


<script>

    /** Validate credentials */
    function recovery_password() {
        
        var email = document.getElementById("email").value;
        var subject = '¡Recupera tu contraseña!';

        $.ajax({
            url: 'https://api.velaaroma.com/v1/users/recovery',
            type: "POST",
            data:'email=' + email + '&subject=' + subject,
            success: function(response){ 

                $('#result').empty();

                    document.getElementById("result").innerHTML = "<p style='color:#000000;font-weight: bold;'>" + "Le enviamos un correo electrónico para recuperar su contraseña." + "</p>";
            }
        });
    }

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    } 

</script>