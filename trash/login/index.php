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
        
        <title> Visor Financiero - Login </title>    
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <meta name="author" content="Visor Financiero">
        <meta name="description" content="Login Visor Financiero">
        <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
        
        <link rel="icon" type="image/x-icon" href="/../images/favicon.ico">
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
                        Bienvenido
                    </div>
                    <div id="loginform">
                        <input type="text" name="usuario" placeholder="Usuario" id="emp_user" required>
                        
                        <input type="password" placeholder="Contraseña" name="password" id="emp_password" required>
                        
                        <button type="submit" title="Ingresar" name="Ingresar" onclick="validate_credentials()">Entrar</button>
                    </div>
                    <div class="pie-form">
                        <a href="./../forgot_password/">¿Perdiste tu contraseña?</a>
                        <a href="./../register/">¿No tienes Cuenta? Registrate</a>
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

    /** Validate credentials */
    function validate_credentials() {
        
        var user = document.getElementById("emp_user").value;
        var pass = document.getElementById("emp_password").value;
    
        $.ajax({
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/users/eyeknow/login',
            type: "GET",
            data:'user=' + user + '&pass=' + pass,
            success: function(response){ 

                $('#result').empty();

                if(response.id > 0){
                    setCookie("username",response.info[0].user,1);
                    setCookie("menuHorizontal",response.info[0].menu_horizontal,1);
                    setCookie("menuVertical",response.info[0].menu_vertical,1);
                    
                    console.log("redireccionamiento a home");
                    window.location = "/../home/";
                } else {
                    console.log("Usuario y/o contraseña incorrectos.");
                    document.getElementById("result").innerHTML = "<p style='color:red;font-weight: bold;'>" + "Usuario y/o contraseña incorrectos." + "</p>";
                }
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