<?php 

include "./../eyeknow-config-web/init.php";

?> 

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma - Login</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Login Vela Aroma">
    <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
    <link rel="icon" type="image/x-icon" href="/../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./../general/general.css">

</head>
<body>
    <div id="contenedor">
        <div id="central">
            <div id="login">
                <div class="titulo">
                    Hola!
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
                <a href="./../">Volver</a>
            </div>
        </div>
    </div>
    <?php include_once "../js/js.php"; ?> 
</body>
</html>

<script>
    function validate_credentials() {
        var user = document.getElementById("emp_user").value;
        var pass = document.getElementById("emp_password").value;
    
        $.ajax({
            url: 'https://api.velaaroma.com/v1/users/validate',
            type: "POST",
            data:'username=' + user + '&password=' + pass,
            success: function(response){ 
                $('#result').empty();
                if(response.info[0].id > 0){
                    if(response.info[0].virtual_address_is_validated == '1'){
                        setCookie("username",response.info[0].username,7);
                        setCookie("name",response.info[0].name,7);
                        window.location = "/../index.php";
                    } else {
                        console.log("Correo no validado.");
                        document.getElementById("result").innerHTML = "<p>Valida tu correo electrónico que te enviamos al correo proporcionado.</p>";
                    }
                } else {
                    console.log("Usuario y/o contraseña incorrectos.");
                    document.getElementById("result").innerHTML = "<p>Usuario y/o contraseña incorrectos.</p>";
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
