<?php include "./../eyeknow-config-web/init.php"; ?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma - Register</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma ">
    <meta name="description" content="Registro Vela Aroma">
    <meta name="keywords" content="Formulario Acceso, Formulario de LogIn">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./../general/general.css">
    <style>
        .input-row {
            display: flex;
            gap: 10px;
        }
        .input-row input {
            flex: 1;
        }
        .section-divider {
            margin: 20px 0;
            border-top: 1px solid #ccc;
        }
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
    </style>
</head>
<body>
    <div id="contenedor">
        <div id="central">
            <div id="login">
                <div class="titulo">Registro</div>
                <div id="loginform">
                    <div id="section1" class="section active">
                        <input type="text" id="username" placeholder="Username" required>
                        <div class="input-row">
                            <input type="text" id="name" placeholder="Nombre" required>
                            <input type="text" id="first_last_name" placeholder="Primer apellido">
                            <input type="text" id="second_last_name" placeholder="Segundo apellido">
                        </div>
                        <input type="password" id="password" placeholder="Contraseña" required>
                        <button type="button" onclick="showSection(2)">Siguiente</button>
                    </div>
                    <div id="section2" class="section">
                        <div class="input-row">
                            <input type="text" id="email" placeholder="Correo electrónico" required>
                            <input type="text" id="telephone" placeholder="Teléfono" required>
                        </div>
                        <button type="button" onclick="showSection(1)">Anterior</button>
                        <button type="button" onclick="showSection(3)">Siguiente</button>
                    </div>
                    <div id="section3" class="section">
                        <div class="section-divider"></div>
                        <input type="text" id="address" placeholder="Calle, número int. y ext." required>
                        <input type="text" id="zipcode" placeholder="Código postal" required>
                        <button type="button" onclick="showSection(2)">Anterior</button>
                        <button type="submit" title="Ingresar" name="Registrar" onclick="register()">Registrar</button>
                    </div>
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
function showSection(sectionNumber) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById('section' + sectionNumber).classList.add('active');
}

function register() {
    var name = document.getElementById("name").value;
    var first_last_name = document.getElementById("first_last_name").value;
    var second_last_name = document.getElementById("second_last_name").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var username = document.getElementById("username").value;
    var address = document.getElementById("address").value;
    var zipcode = document.getElementById("zipcode").value;
    var telephone = document.getElementById("telephone").value;
    var subject = "Vela Aroma - Confirma tu correo electrónico";

    $.ajax({
        url: 'https://api.velaaroma.com/v1/users',
        type: "POST",
        data: 'name=' + name + '&first_last_name=' + first_last_name + '&second_last_name=' 
            + second_last_name + '&email=' + email + '&password=' + password + '&username=' + username 
            + '&zipcode=' + zipcode + '&address=' + address + '&telephone=' + telephone + '&subject=' + subject ,
        success: function(response){ 
          
            if(response.status == 'success'){
                document.getElementById("result").innerHTML = "<p style='color:#000000;font-weight: bold;'>" + response.description + "</p>";
            } else {
                document.getElementById("result").innerHTML = "<p style='color:#000000;font-weight: bold;'>" + response.description + "</p>";
            }
        }
    });
}
</script>
