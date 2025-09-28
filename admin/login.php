<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Vela Aroma</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input[type="password"] {
            padding-right: 45px; /* Espacio para el botón de mostrar/ocultar */
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
            display: none;
        }

        .footer-text {
            margin-top: 30px;
            color: #999;
            font-size: 14px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            width: 20px;
            height: 20px;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .paste-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-candle-holder"></i> Vela Aroma</h1>
            <p>Panel de Administración</p>
        </div>

        <div id="errorMessage" class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            Usuario o contraseña incorrectos
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="username">Usuario</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" required 
                           placeholder="Ingresa tu usuario" 
                           autocomplete="username"
                           spellcheck="false">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" required 
                           placeholder="Ingresa tu contraseña o pega aquí (Ctrl+V)"
                           autocomplete="current-password"
                           spellcheck="false">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <div class="paste-hint">
                    💡 Puedes pegar la contraseña con Ctrl+V
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>

        <div class="footer-text">
            <i class="fas fa-shield-alt"></i> Acceso restringido
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');
            
            // Credenciales hardcodeadas
            if (username === 'velaaroma' && password === 'v3L44r0m4#') {
                // Login exitoso - guardar en sessionStorage
                sessionStorage.setItem('admin_logged_in', 'true');
                sessionStorage.setItem('admin_user', username);
                
                // Redirigir al admin panel
                window.location.href = './';
            } else {
                // Mostrar error
                errorMessage.style.display = 'block';
                
                // Limpiar campos
                document.getElementById('password').value = '';
                document.getElementById('username').focus();
                
                // Ocultar error después de 3 segundos
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 3000);
            }
        });

        // Si ya está logueado, redirigir
        if (sessionStorage.getItem('admin_logged_in') === 'true') {
            window.location.href = './';
        }

        // Permitir pegar en el campo de contraseña
        const passwordField = document.getElementById('password');
        const usernameField = document.getElementById('username');
        
        // Permitir pegar en ambos campos
        [passwordField, usernameField].forEach(field => {
            field.addEventListener('paste', function(e) {
                // Permitir explícitamente el pegado
                e.stopPropagation();
                return true;
            });
            
            // También permitir otras operaciones de teclado
            field.addEventListener('keydown', function(e) {
                // Permitir Ctrl+V, Ctrl+C, Ctrl+X, Ctrl+A
                if (e.ctrlKey && (e.key === 'v' || e.key === 'c' || e.key === 'x' || e.key === 'a')) {
                    return true;
                }
            });
        });

        // Función para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>