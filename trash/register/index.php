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
        
        <title> Visor Financiero - Register </title>    
        
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
                        Registro
                    </div>
                    <div id="loginform">
                        <input type="text" id="name" placeholder="Nombre" required>
                        <input type="text" id="first_last_name" placeholder="Primer apellido" required>
                        <input type="text" id="second_last_name" placeholder="Segundo apellido">
                        <input type="text" id="email" placeholder="Correo electrónico" required>
                        
                        <input type="password" placeholder="Contraseña" id="password" required>
                        
                        <button type="submit" title="Ingresar" name="Registrar" onclick="register()">Registrar</button>
                    </div>
                    <div id="result"></div>
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

    function register() {
        
        var name = document.getElementById("name").value;
        var first_last_name = document.getElementById("first_last_name").value;
        var second_last_name = document.getElementById("second_last_name").value;
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;
    
        $.ajax({
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/users/register',
            type: "POST",
            data:'name=' + name + '&first_last_name=' + first_last_name + '&second_last_name=' 
                + second_last_name + '&email=' + email + '&password=' + password + '&organization=visor_financiero',
            success: function(response){ 

                document.getElementById("result").innerHTML = "<p style='color:white;font-weight: bold;'>" + "Confirma el correo electrónico que te enviamos a la dirección que registraste. " + "</p>";
            }
        });
    }

</script>