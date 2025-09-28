<?php
/**
 * Controlador de Usuarios
 * Maneja registro, login, validación, recuperación de contraseña
 */

class UsersController {
    
    public function handleRequest($action, $method, $param) {
        switch ($action) {
            case '': // GET /api/users - listar usuarios (admin)
                if ($method === 'GET') {
                    $this->listUsers();
                } elseif ($method === 'POST') {
                    $this->registerUser();
                }
                break;
                
            case 'validate':
                $this->validateUser();
                break;
                
            case 'confirmation':
                $this->confirmUser();
                break;
                
            case 'recovery':
                $this->recoveryPassword();
                break;
                
            case 'new_password':
                $this->newPassword();
                break;
                
            default:
                sendJsonResponse(['error' => 'Acción no encontrada'], 404);
        }
    }
    
    public function registerUser() {
        $data = $this->getPostData();
        
        // Validar campos requeridos
        $required = ['name', 'first_last_name', 'email', 'password', 'username'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendJsonResponse(['error' => "Campo requerido: $field"], 400);
            }
        }
        
        // Validar email
        if (!validateEmail($data['email'])) {
            sendJsonResponse(['error' => 'Email inválido'], 400);
        }
        
        $users = JsonDB::read(USERS_FILE);
        
        // Verificar si el usuario ya existe
        if (JsonDB::findByField($users, 'email', $data['email'])) {
            sendJsonResponse(['error' => 'El email ya está registrado'], 400);
        }
        
        if (JsonDB::findByField($users, 'username', $data['username'])) {
            sendJsonResponse(['error' => 'El username ya está en uso'], 400);
        }
        
        // Crear nuevo usuario
        $user = [
            'id' => JsonDB::generateId(),
            'name' => $data['name'],
            'first_last_name' => $data['first_last_name'],
            'second_last_name' => $data['second_last_name'] ?? '',
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => hashPassword($data['password']),
            'address' => $data['address'] ?? '',
            'zipcode' => $data['zipcode'] ?? '',
            'telephone' => $data['telephone'] ?? '',
            'virtual_address_is_validated' => '0',
            'validation_code' => generateCode(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $user;
        JsonDB::write(USERS_FILE, $users);
        
        // Simular envío de email (en producción aquí enviarías el email real)
        $this->simulateEmailSend($user['email'], $user['validation_code']);
        
        sendJsonResponse(['message' => 'Usuario registrado exitosamente', 'user_id' => $user['id']]);
    }
    
    public function validateUser() {
        $data = $this->getPostData();
        
        if (empty($data['username']) || empty($data['password'])) {
            sendJsonResponse(['error' => 'Username y password requeridos'], 400);
        }
        
        $users = JsonDB::read(USERS_FILE);
        $user = JsonDB::findByField($users, 'username', $data['username']);
        
        if (!$user || !verifyPassword($data['password'], $user['password'])) {
            sendJsonResponse(['info' => [['id' => 0]]], 200);
            return;
        }
        
        // Usuario válido
        unset($user['password']); // No enviar la contraseña
        sendJsonResponse(['info' => [$user]]);
    }
    
    public function confirmUser() {
        $data = $this->getPostData();
        
        if (empty($data['code']) || empty($data['virtual_address'])) {
            sendJsonResponse(['error' => 'Código y email requeridos'], 400);
        }
        
        $users = JsonDB::read(USERS_FILE);
        
        foreach ($users as &$user) {
            if ($user['email'] === $data['virtual_address'] && 
                $user['validation_code'] === $data['code']) {
                
                $user['virtual_address_is_validated'] = '1';
                $user['validation_code'] = null;
                
                JsonDB::write(USERS_FILE, $users);
                sendJsonResponse(['message' => 'Usuario confirmado exitosamente']);
                return;
            }
        }
        
        sendJsonResponse(['error' => 'Código de validación inválido'], 400);
    }
    
    public function recoveryPassword() {
        $data = $this->getPostData();
        
        if (empty($data['email'])) {
            sendJsonResponse(['error' => 'Email requerido'], 400);
        }
        
        $users = JsonDB::read(USERS_FILE);
        
        foreach ($users as &$user) {
            if ($user['email'] === $data['email']) {
                $user['recovery_code'] = generateCode();
                JsonDB::write(USERS_FILE, $users);
                
                // Simular envío de email
                $this->simulateEmailSend($user['email'], $user['recovery_code'], 'recovery');
                
                sendJsonResponse(['message' => 'Código de recuperación enviado']);
                return;
            }
        }
        
        sendJsonResponse(['error' => 'Email no encontrado'], 404);
    }
    
    public function newPassword() {
        $data = $this->getPostData();
        
        if (empty($data['email']) || empty($data['password']) || empty($data['code'])) {
            sendJsonResponse(['error' => 'Email, password y código requeridos'], 400);
        }
        
        $users = JsonDB::read(USERS_FILE);
        
        foreach ($users as &$user) {
            if ($user['email'] === $data['email'] && 
                isset($user['recovery_code']) && 
                $user['recovery_code'] === $data['code']) {
                
                $user['password'] = hashPassword($data['password']);
                unset($user['recovery_code']);
                
                JsonDB::write(USERS_FILE, $users);
                sendJsonResponse(['message' => 'Contraseña actualizada exitosamente']);
                return;
            }
        }
        
        sendJsonResponse(['error' => 'Código de recuperación inválido'], 400);
    }
    
    public function listUsers() {
        $users = JsonDB::read(USERS_FILE);
        
        // Remover contraseñas de la respuesta
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        sendJsonResponse(['users' => $users]);
    }
    
    private function getPostData() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            // Fallback a $_POST para formularios normales y data URL-encoded
            $data = $_POST;
            
            // Si tampoco hay $_POST, parsear input como URL-encoded
            if (empty($data) && !empty($input)) {
                parse_str($input, $data);
            }
        }
        
        return $data ?: [];
    }
    
    private function simulateEmailSend($email, $code, $type = 'validation') {
        // En ambiente de desarrollo, guardamos los "emails" en un archivo
        $emails = JsonDB::read(DATA_DIR . 'sent_emails.json');
        
        $emailData = [
            'to' => $email,
            'code' => $code,
            'type' => $type,
            'sent_at' => date('Y-m-d H:i:s')
        ];
        
        $emails[] = $emailData;
        JsonDB::write(DATA_DIR . 'sent_emails.json', $emails);
        
        // Log para debugging
        error_log("Email simulado enviado a: $email, código: $code, tipo: $type");
    }
}
?>