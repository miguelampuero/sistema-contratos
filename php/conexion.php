<?php
// Archivo para conexión a la base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "sistema_contratos";

$conexion = new mysqli($host, $usuario, $password, $bd);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>