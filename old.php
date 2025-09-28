<?php namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\BaseControlller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client; 

class VelaAromaController extends BaseController{

    public function GetUsers($request, $response, $arg) {
        $pdo = $this->container->get('db');
    
        $query = $pdo->query("SELECT * FROM va_users");
        $result = $query->fetchAll();
    
        $data["users"] = $result;
    
        // Escribe la respuesta en el cuerpo de la respuesta original
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ValidateUserExist($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM va_users where (username = '$username' OR virtual_address = '$username') AND password = '$password'";
        $data["sql"] = $sql;

        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["info"] = $result;
    
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function AddNewUser($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $name = $_POST["name"];
        $first_last_name = $_POST["first_last_name"];
        $second_last_name = $_POST["second_last_name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $username = $_POST["username"];
        $address = $_POST["address"];
        $zipcode = $_POST["zipcode"];
        $telephone = $_POST["telephone"];

        $subject =  $_POST["subject"];
        $randomString = "";

        $query = $pdo->query("SELECT * FROM va_users WHERE virtual_address = '$email'");
        $results = $query->fetchAll();

        if(empty($results)){
           
            $query = $pdo->query("SELECT * FROM va_users WHERE username = '$username'");
            $results = $query->fetchAll();

            if(empty($results)){

                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 9; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                 
                $confirm_link = "http://ec2-3-17-57-199.us-east-2.compute.amazonaws.com/confirmation/index.php?code=$randomString&email=$email";

                $query = $pdo->query("INSERT INTO va_users (name, first_last_name, second_last_name, virtual_address, password, username, telephone, code_validation)
                                                VALUES ('$name', '$first_last_name', '$second_last_name', '$email', '$password', '$username', '$telephone', '$randomString')");

                $query = $pdo->query("SELECT * FROM va_users WHERE username = '$username'");
                $data["users"] = $results = $query->fetchAll();

                $query = $pdo->query("SELECT * FROM va_users WHERE username = '$username' AND password = '$password'");
                $results = $query->fetchAll();

                $user_id = $results{0}->id;

                $query = $pdo->query("INSERT INTO va_address (user_id, address, zip_code)
                VALUES ('$user_id', '$address', '$zipcode')");

                $data["title"] = "El usuario se creo correctamente.";
                $data["status"] = "success";
                $data["description"] = "Se creo correctamente el usuario.";

                // Sent email
                if(!empty($subject)){
                
                $alert = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <!doctype html>
                <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Confirmación de correo electrónico</title>
                    <style type="text/css">
                        body, table, td, a {
                            -webkit-text-size-adjust: 100%;
                            -ms-text-size-adjust: 100%;
                        }
                        table, td {
                            mso-table-lspace: 0pt;
                            mso-table-rspace: 0pt;
                        }
                        img {
                            -ms-interpolation-mode: bicubic;
                        }
                        img, a img {
                            border: 0;
                            height: auto;
                            outline: none;
                            text-decoration: none;
                        }
                        body {
                            height: 100% !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 100% !important;
                        }
                        a[x-apple-data-detectors] {
                            color: inherit !important;
                            text-decoration: none !important;
                            font-size: inherit !important;
                            font-family: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                        }
                        .button {
                            background-color: #000000;
                            border: none;
                            color: white; /* Aquí cambias el color de las letras del botón */
                            padding: 15px 32px;
                            text-align: center;
                            text-decoration: none;
                            display: inline-block;
                            font-size: 16px;
                            margin: 4px 2px;
                            cursor: pointer;
                            border-radius: 5px;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border: 1px solid #eaeaea;
                        }
                        .header {
                            padding: 20px;
                            text-align: center;
                            background: #ffffff;
                        }
                        .header h1 {
                            font-size: 24px;
                            font-weight: 400;
                            margin: 0;
                            color: #000000;
                        }
                        .content {
                            padding: 20px;
                            font-family: Arial, sans-serif;
                            font-size: 16px;
                            line-height: 1.5;
                        }
                        .footer {
                            padding: 20px;
                            text-align: center;
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                            color: #888888;
                        }
                    </style>
                </head>
                <body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
                    <center>
                        <div class="container">
                            <div class="header">
                                <h1>Vela Aroma</h1>
                            </div>
                            <div class="content">
                                <h2>¡Gracias por registrarte!</h2>
                                <p>Por favor confirma tu correo electrónico para que puedas comenzar a comprar dando click en el siguiente botón:</p>
                                <p><a href="'.$confirm_link.'" class="button">Confirma tu correo</a></p>
                                <p>Si tienes problemas para confirmar tu correo electrónico desde el botón, entonces copia y pega el siguiente link en el navegador:</p>
                                <p><a href="'.$confirm_link.'" target="_blank">'.$confirm_link.'</a></p>
                                <p>Si tú no te registraste en Vela Aroma, por favor ignora este correo electrónico.</p>
                            </div>
                            <div class="footer">
                                <p>Este correo electrónico fue enviado desde una dirección solamente de notificaciones que no puede aceptar correo electrónico entrante. Por favor no respondas a este mensaje.</p>
                            </div>
                        </div>
                    </center>
                </body>
                </html>
                ';
                
                require __DIR__ . '/../../vendor/autoload.php';

                $mail = new PHPMailer(true);

                try {
                    //$mail->SMTPDebug = 2;  
                    $mail->isSMTP();                                    
                    $mail->Host       = "email-smtp.us-east-1.amazonaws.com";    
                    $mail->SMTPAuth   = true;                                 
                    $mail->Username   = "AKIAQFXT47ADBVBCAEYX";                   
                    $mail->Password   = "BGntd/GSoS5h3N3GMeq6KSSttmDBISnLyoDsPspu03LJ";                             
                    $mail->SMTPSecure = "tls";           
                    $mail->Port       = "587";     

                    //Recipients
                    $mail->setFrom("no-reply@velaaroma.com", "Vela Aroma");                
                    $bcc_multiple = explode(",", $email);
                    for($w = 0; $w < count($bcc_multiple); $w++){
                        $mail->addAddress(trim($bcc_multiple[$w]));
                    }                

                    $mail->isHTML(true); 
                    $mail->Subject = $subject;
                    $mail->Body    = $alert;
                    $mail->CharSet = "UTF-8";
                    
                    $mail->send();
                    $data["result"] = 'El correo electrónico ha sido enviado.';
                } catch (Exception $e) {
                    $data["result"] = "El correo no pudo ser enviado: {$mail->ErrorInfo}";
                }

                $data["title"] = "El usuario se creo correctamente.";
                $data["status"] = "success";
                $data["description"] = "Valida el correo electrónico que se te envió.";

            }

            } else {
                $data["title"] = "El usuario ya existe.";
                $data["status"] = "error";
                $data["description"] = "Utiliza otro nombre de usuario.";
            }
    

        } else {
            $data["title"] = "Correo electrónico ya existe";
            $data["status"] = "error";
            $data["description"] = "Tu correo ya exite, utiliza otro correo o recupera tu contraseña.";
        }

    
        $sql = "SELECT vlp.name as product_id, vc.aroma, vc.color, vc.cantidad, vc.valor FROM va_cart vc inner join va_list_product vlp on vc.product_id = vlp.id 
        where vc.username = '$username' AND vc.status = 'in_progress' AND vc.deleted_at is null";

        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = $result;

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function UserConfirmation($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $code = $_POST["code"];
        $virtual_address = $_POST["virtual_address"];

        $sql = "SELECT * FROM va_users where virtual_address = '$virtual_address' AND code_validation = '$code'";
        $query = $pdo->query($sql);
    
       if(!empty($query)){
        $data["update"] = $sql = "UPDATE va_users SET  virtual_address_is_validated = '1'  where virtual_address = '$virtual_address' AND code_validation = '$code'";
        $pdo->query($sql);
        
       }
    
       $sql = "SELECT * FROM va_users where virtual_address = '$virtual_address' AND code_validation = '$code'";
       $query = $pdo->query($sql);
       $data["data"] = $result = $query->fetchAll();

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function UserRecovery($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $email = $_POST["email"];
        $subject =  $_POST["subject"];
        $randomString = "";

        $query = $pdo->query("SELECT * FROM va_users WHERE virtual_address = '$email'");
        $results = $query->fetchAll();

        if(!empty($results)){

                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 9; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                 
                $confirm_link = "http://ec2-3-17-57-199.us-east-2.compute.amazonaws.com/forgot_password/recovery_password.php?code=$randomString&email=$email";

                $sql = "UPDATE va_users SET  code_validation = '$randomString'  where virtual_address = '$email'";
                $pdo->query($sql);

                // Sent email
                if(!empty($email)){
                
                $alert = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <!doctype html>
                <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Confirmación de correo electrónico</title>
                    <style type="text/css">
                        body, table, td, a {
                            -webkit-text-size-adjust: 100%;
                            -ms-text-size-adjust: 100%;
                        }
                        table, td {
                            mso-table-lspace: 0pt;
                            mso-table-rspace: 0pt;
                        }
                        img {
                            -ms-interpolation-mode: bicubic;
                        }
                        img, a img {
                            border: 0;
                            height: auto;
                            outline: none;
                            text-decoration: none;
                        }
                        body {
                            height: 100% !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 100% !important;
                        }
                        a[x-apple-data-detectors] {
                            color: inherit !important;
                            text-decoration: none !important;
                            font-size: inherit !important;
                            font-family: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                        }
                        .button {
                            background-color: #000000;
                            border: none;
                            color: white; /* Aquí cambias el color de las letras del botón */
                            padding: 15px 32px;
                            text-align: center;
                            text-decoration: none;
                            display: inline-block;
                            font-size: 16px;
                            margin: 4px 2px;
                            cursor: pointer;
                            border-radius: 5px;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border: 1px solid #eaeaea;
                        }
                        .header {
                            padding: 20px;
                            text-align: center;
                            background: #ffffff;
                        }
                        .header h1 {
                            font-size: 24px;
                            font-weight: 400;
                            margin: 0;
                            color: #000000;
                        }
                        .content {
                            padding: 20px;
                            font-family: Arial, sans-serif;
                            font-size: 16px;
                            line-height: 1.5;
                        }
                        .footer {
                            padding: 20px;
                            text-align: center;
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                            color: #888888;
                        }
                    </style>
                </head>
                <body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
                    <center>
                        <div class="container">
                            <div class="header">
                                <h1>Vela Aroma</h1>
                            </div>
                            <div class="content">
                                <h2>¡Crea una nueva contraseña!</h2>
                                <p>Por favor recupera tu contraseña para que puedas continuar comprando dando click en el siguiente botón:</p>
                                <p><a href="'.$confirm_link.'" class="button">Crea tu nueva contraseña</a></p>
                                <p>Si tienes problemas para confirmar tu correo electrónico desde el botón, entonces copia y pega el siguiente link en el navegador:</p>
                                <p><a href="'.$confirm_link.'" target="_blank">'.$confirm_link.'</a></p>
                                <p>Si tú no solicitaste cambiar tu contraeña en Vela Aroma, por favor ignora este correo electrónico.</p>
                            </div>
                            <div class="footer">
                                <p>Este correo electrónico fue enviado desde una dirección solamente de notificaciones que no puede aceptar correo electrónico entrante. Por favor no respondas a este mensaje.</p>
                            </div>
                        </div>
                    </center>
                </body>
                </html>
                ';
                
                require __DIR__ . '/../../vendor/autoload.php';

                $mail = new PHPMailer(true);

                try {
                    //$mail->SMTPDebug = 2;  
                    $mail->isSMTP();                                    
                    $mail->Host       = "email-smtp.us-east-1.amazonaws.com";    
                    $mail->SMTPAuth   = true;                                 
                    $mail->Username   = "AKIAQFXT47ADBVBCAEYX";                   
                    $mail->Password   = "BGntd/GSoS5h3N3GMeq6KSSttmDBISnLyoDsPspu03LJ";                             
                    $mail->SMTPSecure = "tls";           
                    $mail->Port       = "587";     

                    //Recipients
                    $mail->setFrom("no-reply@velaaroma.com", "Vela Aroma");                
                    $bcc_multiple = explode(",", $email);
                    for($w = 0; $w < count($bcc_multiple); $w++){
                        $mail->addAddress(trim($bcc_multiple[$w]));
                    }                

                    $mail->isHTML(true); 
                    $mail->Subject = $subject;
                    $mail->Body    = $alert;
                    $mail->CharSet = "UTF-8";
                    
                    $mail->send();
                    $data["result"] = 'El correo electrónico ha sido enviado.';
                } catch (Exception $e) {
                    $data["result"] = "El correo no pudo ser enviado: {$mail->ErrorInfo}";
                }
            }

             
            } else {
                $data["result"] = 'El correo electrónico ha sido enviado.';
            }

    
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function UserNewPassword($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $code = $_POST["code"];
        $virtual_address = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM va_users where virtual_address = '$virtual_address' AND code_validation = '$code'";
        $query = $pdo->query($sql);
    
       if(!empty($query)){
        $data["update"] = $sql = "UPDATE va_users SET  password = '$password', virtual_address_is_validated = '1'   where virtual_address = '$virtual_address' AND code_validation = '$code'";
        $pdo->query($sql);
        
       }
    
       $sql = "SELECT * FROM va_users where virtual_address = '$virtual_address' AND code_validation = '$code'";
       $query = $pdo->query($sql);
       $data["data"] = $result = $query->fetchAll();

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ObtainProducts($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $username = $_GET["username"];
        $status = $_GET["status"];
       
        $sql = "SELECT * FROM va_cart where username = '$username' AND status = '$status' AND deleted_at is null";
        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = count($result);

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ObtainProductsSell($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $username = $_GET["username"];
        $status = $_GET["status"];
       
        $sql = "SELECT vc.id, vlp.name as product_id, vc.aroma, vc.color, vc.cantidad, vc.valor FROM va_cart vc inner join va_list_product vlp on vc.product_id = vlp.id 
        where vc.username = '$username' AND vc.status = '$status' AND vc.deleted_at is null";
       //$sql = "SELECT * FROM va_cart where deleted_at is null";

        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = $result;

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function AddProducts($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $username = $_POST["username"];
        $status = $_POST["status"];
        $color = $_POST["color"];
        $aroma = $_POST["aroma"];
        $cantidad = $_POST["cantidad"];
        $product_id = $_POST["product_id"];
       
        $data["sql_product_in_cart"] = $sql = "SELECT * FROM va_cart where product_id = '$product_id' AND aroma = '$aroma' AND color = '$color' AND username = '$username' AND status = 'in_progress'";
        $query = $pdo->query($sql);
       $data["product_in_cart"] = $result = $query->fetchAll();

        if(empty($result)){
            $sql = "SELECT * FROM va_list_product where id = '$product_id'";
            $query = $pdo->query($sql);
            $result = $query->fetchAll();
        
            $mayoreo = $result{0}->mayoreo;
            $menudeo = $result{0}->menudeo;

            if($cantidad > 4){
                $valor = $cantidad * $mayoreo;
            } else {
                $valor = $cantidad * $menudeo;
            }

            $query = $pdo->query("INSERT INTO va_cart (username, product_id, color, aroma, cantidad, valor)
            VALUES ('$username', '$product_id', '$color', '$aroma', '$cantidad', '$valor')");

        } else {
            $cantidad = $result{0}->cantidad + $cantidad;
            $cart_id = $result{0}->id;
            
            $sql = "SELECT * FROM va_list_product where id = '$product_id'";
            $query = $pdo->query($sql);
            $result = $query->fetchAll();
        
            $mayoreo = $result{0}->mayoreo;
            $menudeo = $result{0}->menudeo;

            if($cantidad > 3){
                $valor = $cantidad * $mayoreo;
            } else {
                $valor = $cantidad * $menudeo;
            }

            $sql = "UPDATE va_cart SET cantidad = '$cantidad', valor = '$valor' WHERE id = '$cart_id'";
            $pdo->query($sql);

        } 
        
        $data["data"] = "OK";
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ObtainProductsByCategory($request, $response, $arg) {

        $pdo = $this->container->get('db');
    
        $category = $_GET["category"];
       
        $sql = "SELECT * FROM va_list_product where category = '$category' AND deleted_at is null";
        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = $result;

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ObtainProductsAll($request, $response, $arg) {

        $pdo = $this->container->get('db');
           
        $sql = "SELECT * FROM va_list_product";
        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = $result;

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ProductsUpdate($request, $response, $arg) {

        $pdo = $this->container->get('db');
           
        $column = $_POST["column"];
        $id = $_POST["id"];
        $value = $_POST["val"];

        $data["update"] = $sql = "UPDATE va_list_product SET  $column = '$value'  where id = '$id'";
        $pdo->query($sql);

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function ProductsCreate($request, $response, $arg) {

        $pdo = $this->container->get('db');
           
        $name = $_POST["name"];
        $description = $_POST["description"];
        $mayoreo = $_POST["mayoreo"];
        $menudeo = $_POST["menudeo"];
        $largo = $_POST["largo"];
        $alto = $_POST["alto"];
        $ancho = $_POST["ancho"];
        $category = $_POST["category"];
        $url = $_POST["url"];

        $query = $pdo->query("INSERT INTO va_list_product (name, description, mayoreo, menudeo, largo, alto, ancho, category, url)
                            VALUES ('$name', '$description', '$mayoreo', '$menudeo', '$largo', '$alto', '$ancho', '$category', '$url' )");


        $sql = "SELECT * FROM va_list_product WHERE name = '$name' AND mayoreo = '$mayoreo' AND url = '$url' AND category = '$category'";
        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $data["products"] = $result;

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function UpdateStatusCart($request, $response, $arg) {

        $pdo = $this->container->get('db');
           
        $username = $_POST["username"];
        $totalQuantity = $_POST["totalQuantity"];
        $totalEnvio = $_POST["totalEnvio"];
        $totalProductos = $_POST["totalProductos"];
        $totalValue = $_POST["totalValue"];
        
        $data["update"] = $sql = "UPDATE va_cart SET  status =  'completed'  where status = 'in_progress' AND username = '$username'";
        $pdo->query($sql);


        $sql = "SELECT * FROM va_users where user = '$username'";
        $query = $pdo->query($sql);
        $result = $query->fetchAll();
    
        $email = $result{0}->virtual_address;

        $alert = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <!doctype html>
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Confirmación de compra</title>
                <style type="text/css">
                    body, table, td, a {
                        -webkit-text-size-adjust: 100%;
                        -ms-text-size-adjust: 100%;
                    }
                    table, td {
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                    }
                    img {
                        -ms-interpolation-mode: bicubic;
                    }
                    img, a img {
                        border: 0;
                        height: auto;
                        outline: none;
                        text-decoration: none;
                    }
                    body {
                        height: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        width: 100% !important;
                        background-color: #f4f4f4;
                    }
                    a[x-apple-data-detectors] {
                        color: inherit !important;
                        text-decoration: none !important;
                        font-size: inherit !important;
                        font-family: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                    }
                    .button {
                        background-color: #000000;
                        border: none;
                        color: white;
                        padding: 15px 32px;
                        text-align: center;
                        text-decoration: none;
                        display: inline-block;
                        font-size: 16px;
                        margin: 4px 2px;
                        cursor: pointer;
                        border-radius: 5px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: #ffffff;
                        border: 1px solid #eaeaea;
                    }
                    .header {
                        padding: 20px;
                        text-align: center;
                        background: #ffffff;
                    }
                    .header h1 {
                        font-size: 24px;
                        font-weight: 400;
                        margin: 0;
                        color: #000000;
                    }
                    .content {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        line-height: 1.5;
                    }
                    .footer {
                        padding: 20px;
                        text-align: center;
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        color: #888888;
                    }
                    .product-list {
                        margin: 20px 0;
                    }
                    .product-list th, .product-list td {
                        padding: 10px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                    }
                </style>
            </head>
            <body style="margin: 0 !important; padding: 0 !important; background-color: #f4f4f4;">
                <center>
                    <div class="container">
                        <div class="header">
                            <h1>Gracias por tu compra en Vela Aroma, '.$username.'!</h1>
                        </div>
                        <div class="content">
                            <h2>Detalles de tu compra:</h2>
                           
                            <p><strong>Número de productos:</strong> '.$totalQuantity.'</p>
                            <p><strong>Envío:</strong> $'.$totalEnvio.'</p>
                            <p><strong>Total Productos:</strong> $'.$productos.'</p>
                            <p><strong>Total incluyendo envío:</strong> $'.$totalValue.'</p>
                            <p>Si tienes alguna pregunta sobre tu compra, no dudes en contactarnos.</p>
                        </div>
                        <div class="footer">
                            <p>Este correo es solo una confirmación de tu compra. Por favor, no respondas a este mensaje.</p>
                        </div>
                    </div>
                </center>
            </body>
            </html>
        ';



        $email = $email . ",ivan.balderas.serrano@gmail.com";
        $subject =  "Vela Aroma | Compra exitosa";

        require __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            //$mail->SMTPDebug = 2;  
            $mail->isSMTP();                                    
            $mail->Host       = "email-smtp.us-east-1.amazonaws.com";    
            $mail->SMTPAuth   = true;                                 
            $mail->Username   = "AKIAQFXT47ADBVBCAEYX";                   
            $mail->Password   = "BGntd/GSoS5h3N3GMeq6KSSttmDBISnLyoDsPspu03LJ";                             
            $mail->SMTPSecure = "tls";           
            $mail->Port       = "587";     

            //Recipients
            $mail->setFrom("no-reply@velaaroma.com", "Vela Aroma");                
            $bcc_multiple = explode(",", $email);
            for($w = 0; $w < count($bcc_multiple); $w++){
                $mail->addAddress(trim($bcc_multiple[$w]));
            }                

            $mail->isHTML(true); 
            $mail->Subject = $subject;
            $mail->Body    = $alert;
            $mail->CharSet = "UTF-8";
            
            $mail->send();
            $data["result"] = 'El correo electrónico ha sido enviado.';
        } catch (Exception $e) {
            $data["result"] = "El correo no pudo ser enviado: {$mail->ErrorInfo}";
        }
           
        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function UpdateStatusCartv2($request, $response, $arg) {

        $pdo = $this->container->get('db');
           
        $username = $_POST["username"];
        $status = $_POST["status"];
        $product_id = $_POST["product_id"];

        $data["update"] = $sql = "UPDATE va_cart SET  status = '$status' where id = '$product_id' AND username = '$username'";
        $pdo->query($sql);

        $response->getBody()->write(json_encode($data));
    
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
