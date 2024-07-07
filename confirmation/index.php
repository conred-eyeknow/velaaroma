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
        
        <title> Vela Aroma - Login </title>    
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <meta name="author" content="Vela Aroma">
        <meta name="description" content="Login Vela Aroma">
        <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
        
        <link rel="icon" type="image/x-icon" href="/../images/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
        

    </head>
    
    <body>
    Code: <?=  $_GET['code'] ?>

        <?php include_once "../js/js.php"; ?> 
    </body>
</html>

<script>
    // Esperar a que el documento esté completamente cargado
    $(document).ready(function() {
        confirmation();
    });

    /** Validate credentials */
    function confirmation() {
        var code = '<?php echo htmlspecialchars($_GET['code']); ?>';
        var email = '<?php echo htmlspecialchars($_GET['email']); ?>';

        console.log("Code:", code);
        console.log("Email:", email);

        $.ajax({
            url: 'https://api.velaaroma.com/v1/users/confirmation',
            type: "POST",
            data: {
                code: code,
                virtual_address: email
            },
            success: function(response){ 
                console.log("Response:", response);
                window.location.href = "../index.php";
            },
            error: function(xhr, status, error) {
                console.log("Error:", error);
            }
        });
    }

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    } 
</script>
