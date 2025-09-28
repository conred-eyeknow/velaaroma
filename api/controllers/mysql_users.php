<?php
/**
 * Controlador de Usuarios MySQL
 * Migrado de la API externa original
 */

class MySQLUsersController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case '': // GET /api/users - listar usuarios (admin)
                if ($method === 'GET') {
                    $this->getUsers();
                } elseif ($method === 'POST') {
                    $this->addNewUser();
                }
                break;
                
            case 'validate':
                $this->validateUserExist();
                break;
                
            case 'confirmation':
                $this->userConfirmation();
                break;
                
            case 'recovery':
                $this->userRecovery();
                break;
                
            case 'new_password':
                $this->userNewPassword();
                break;
                
            default:
                sendJsonResponse(['error' => 'Acción no encontrada'], 404);
        }
    }
    
    public function getUsers() {
        $sql = "SELECT * FROM va_users";
        $result = MySQLDB::fetchAll($sql);
        
        $data["users"] = $result;
        sendJsonResponse($data);
    }
    
    public function validateUserExist() {
        $data = $this->getPostData();
        
        $username = $data["username"] ?? '';
        $password = $data["password"] ?? '';
        
        if (empty($username) || empty($password)) {
            sendJsonResponse(['info' => [['id' => 0]]], 200);
            return;
        }
        
        $sql = "SELECT * FROM va_users WHERE (username = ? OR virtual_address = ?) AND password = ?";
        $result = MySQLDB::fetchAll($sql, [$username, $username, $password]);
        
        $data["info"] = $result;
        sendJsonResponse($data);
    }
    
    public function addNewUser() {
        $data = $this->getPostData();
        
        // Validar campos requeridos
        $required = ['name', 'first_last_name', 'email', 'password', 'username'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendJsonResponse(['error' => "Campo requerido: $field"], 400);
            }
        }
        
        $name = $data["name"];
        $first_last_name = $data["first_last_name"];
        $second_last_name = $data["second_last_name"] ?? '';
        $email = $data["email"];
        $password = $data["password"];
        $username = $data["username"];
        $address = $data["address"] ?? '';
        $zipcode = $data["zipcode"] ?? '';
        $telephone = $data["telephone"] ?? '';
        $subject = $data["subject"] ?? '';
        
        // Verificar si el email ya existe
        $sql = "SELECT * FROM va_users WHERE virtual_address = ?";
        $existingEmail = MySQLDB::fetchAll($sql, [$email]);
        
        if (!empty($existingEmail)) {
            sendJsonResponse([
                'title' => 'Correo electrónico ya existe',
                'status' => 'error',
                'description' => 'Tu correo ya existe, utiliza otro correo o recupera tu contraseña.'
            ]);
            return;
        }
        
        // Verificar si el username ya existe
        $sql = "SELECT * FROM va_users WHERE username = ?";
        $existingUser = MySQLDB::fetchAll($sql, [$username]);
        
        if (!empty($existingUser)) {
            sendJsonResponse([
                'title' => 'El usuario ya existe',
                'status' => 'error',
                'description' => 'Utiliza otro nombre de usuario.'
            ]);
            return;
        }
        
        // Generar código de validación
        $randomString = generateValidationCode();
        
        // Insertar usuario
        $sql = "INSERT INTO va_users (name, first_last_name, second_last_name, virtual_address, password, username, telephone, code_validation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        MySQLDB::execute($sql, [$name, $first_last_name, $second_last_name, $email, $password, $username, $telephone, $randomString]);
        
        // Obtener el ID del usuario recién creado
        $sql = "SELECT * FROM va_users WHERE username = ? AND password = ?";
        $userResult = MySQLDB::fetchAll($sql, [$username, $password]);
        
        if (!empty($userResult)) {
            $user_id = $userResult[0]->id;
            
            // Insertar dirección si se proporcionó
            if (!empty($address)) {
                $sql = "INSERT INTO va_address (user_id, address, zip_code) VALUES (?, ?, ?)";
                MySQLDB::execute($sql, [$user_id, $address, $zipcode]);
            }
        }
        
        // Simular envío de email de confirmación
        if (!empty($subject)) {
            $confirm_link = "http://localhost:8000/confirmation/index.php?code=$randomString&email=$email";
            $emailBody = $this->generateConfirmationEmail($confirm_link);
            sendEmail($email, $subject, $emailBody);
        }
        
        // Obtener productos del carrito (si existen)
        $sql = "SELECT vlp.name as product_id, vc.aroma, vc.color, vc.cantidad, vc.valor 
                FROM va_cart vc 
                INNER JOIN va_list_product vlp ON vc.product_id = vlp.id 
                WHERE vc.username = ? AND vc.status = 'in_progress' AND vc.deleted_at IS NULL";
        $cartProducts = MySQLDB::fetchAll($sql, [$username]);
        
        sendJsonResponse([
            'title' => 'El usuario se creó correctamente',
            'status' => 'success',
            'description' => 'Valida el correo electrónico que se te envió.',
            'products' => $cartProducts
        ]);
    }
    
    public function userConfirmation() {
        $data = $this->getPostData();
        
        $code = $data["code"] ?? '';
        $virtual_address = $data["virtual_address"] ?? '';
        
        if (empty($code) || empty($virtual_address)) {
            sendJsonResponse(['error' => 'Código y email requeridos'], 400);
        }
        
        // Verificar código
        $sql = "SELECT * FROM va_users WHERE virtual_address = ? AND code_validation = ?";
        $user = MySQLDB::fetchAll($sql, [$virtual_address, $code]);
        
        if (!empty($user)) {
            // Actualizar usuario como validado
            $sql = "UPDATE va_users SET virtual_address_is_validated = '1' WHERE virtual_address = ? AND code_validation = ?";
            MySQLDB::execute($sql, [$virtual_address, $code]);
            
            sendJsonResponse(['message' => 'Usuario confirmado exitosamente']);
        } else {
            sendJsonResponse(['error' => 'Código de validación inválido'], 400);
        }
    }
    
    public function userRecovery() {
        $data = $this->getPostData();
        
        $email = $data["email"] ?? '';
        $subject = $data["subject"] ?? 'Recupera tu contraseña - Vela Aroma';
        
        if (empty($email)) {
            sendJsonResponse(['error' => 'Email requerido'], 400);
        }
        
        // Verificar si el usuario existe
        $sql = "SELECT * FROM va_users WHERE virtual_address = ?";
        $user = MySQLDB::fetchAll($sql, [$email]);
        
        if (!empty($user)) {
            // Generar nuevo código
            $randomString = generateValidationCode();
            
            // Actualizar código de validación
            $sql = "UPDATE va_users SET code_validation = ? WHERE virtual_address = ?";
            MySQLDB::execute($sql, [$randomString, $email]);
            
            // Enviar email
            $confirm_link = "http://localhost:8000/forgot_password/recovery_password.php?code=$randomString&email=$email";
            $emailBody = $this->generateRecoveryEmail($confirm_link);
            sendEmail($email, $subject, $emailBody);
        }
        
        // Siempre responder que se envió (por seguridad)
        sendJsonResponse(['result' => 'El correo electrónico ha sido enviado.']);
    }
    
    public function userNewPassword() {
        $data = $this->getPostData();
        
        $code = $data["code"] ?? '';
        $virtual_address = $data["email"] ?? '';
        $password = $data["password"] ?? '';
        
        if (empty($code) || empty($virtual_address) || empty($password)) {
            sendJsonResponse(['error' => 'Código, email y contraseña requeridos'], 400);
        }
        
        // Verificar código
        $sql = "SELECT * FROM va_users WHERE virtual_address = ? AND code_validation = ?";
        $user = MySQLDB::fetchAll($sql, [$virtual_address, $code]);
        
        if (!empty($user)) {
            // Actualizar contraseña y marcar como validado
            $sql = "UPDATE va_users SET password = ?, virtual_address_is_validated = '1' WHERE virtual_address = ? AND code_validation = ?";
            MySQLDB::execute($sql, [$password, $virtual_address, $code]);
            
            sendJsonResponse(['message' => 'Contraseña actualizada exitosamente']);
        } else {
            sendJsonResponse(['error' => 'Código de recuperación inválido'], 400);
        }
    }
    
    private function getPostData() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $_POST;
            
            if (empty($data) && !empty($input)) {
                parse_str($input, $data);
            }
        }
        
        return $data ?: [];
    }
    
    private function generateConfirmationEmail($confirm_link) {
        return '
        <!DOCTYPE html>
        <html>
        <head><title>Confirmación de correo electrónico</title></head>
        <body>
            <h1>Vela Aroma</h1>
            <h2>¡Gracias por registrarte!</h2>
            <p>Por favor confirma tu correo electrónico haciendo clic en el siguiente enlace:</p>
            <p><a href="'.$confirm_link.'">Confirmar correo</a></p>
            <p>Si no te registraste en Vela Aroma, ignora este correo.</p>
        </body>
        </html>';
    }
    
    private function generateRecoveryEmail($confirm_link) {
        return '
        <!DOCTYPE html>
        <html>
        <head><title>Recuperar contraseña</title></head>
        <body>
            <h1>Vela Aroma</h1>
            <h2>¡Crea una nueva contraseña!</h2>
            <p>Para recuperar tu contraseña, haz clic en el siguiente enlace:</p>
            <p><a href="'.$confirm_link.'">Crear nueva contraseña</a></p>
            <p>Si no solicitaste cambiar tu contraseña, ignora este correo.</p>
        </body>
        </html>';
    }
}
?>