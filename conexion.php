<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "clinica_san_pablo";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Error de conexion: ".$conn->connect_error);
}
else {
    echo "Conexion exitosa!";
}
?>