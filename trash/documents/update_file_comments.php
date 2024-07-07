<?php
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 header("Access-Control-Allow-Origin: *");
 header("Content-Type: text/html;charset=utf-8"); 


#-- Se agrega la base de datos de Alertas
require_once("../dbcontroller.php");
$db_handle = new DBEyeknow();


$datetime = date('Y-m-d H:i:s') ;
$datetime_name = str_replace( array(" ", "/", ":", "T", "-"), "", $datetime);

$path = $_FILES['archivo']['name'];
$data["ext"] = $ext = pathinfo($path, PATHINFO_EXTENSION);


$nombre = $datetime_name . "." . $ext;

$guardado = $_FILES['archivo']['tmp_name'];

$data["url"] = $url = "http://portal.visorfinanciero.info/documents/" . $nombre;
 
move_uploaded_file($guardado, $nombre);

$data["sql"] = $sql = "Update vf_comments SET link = '$url' WHERE id = '" . $_POST["value_id"] . "'";
$faq_id = $db_handle->executeUpdate($sql);

echo json_encode($data);

header('Location: http://portal.visorfinanciero.info/comments/admin.php', true);
die();


?>