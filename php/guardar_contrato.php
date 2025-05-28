<?php
require_once "conexion.php";

// Obtener datos del formulario (asegúrate que los nombres coincidan con registrar.html)
$empresa = $conexion->real_escape_string($_POST['nombre_empresa']);
$ruc = $conexion->real_escape_string($_POST['ruc']);
$telefono = $conexion->real_escape_string($_POST['telefono']);
$fecha = $conexion->real_escape_string($_POST['fecha']);
$descripcion = $conexion->real_escape_string($_POST['descripcion']);
$cantidad = floatval($_POST['cantidad']);
$importe_unitario = floatval($_POST['importe_unitario']);
$total = floatval($_POST['total']);
$a_cuenta = floatval($_POST['a_cuenta']);
$saldo = floatval($_POST['saldo']);

// Insertar en la tabla contratos (ajusta los nombres de columnas si es necesario)
$sql = "INSERT INTO contratos (nombre_empresa, ruc, telefono, fecha, descripcion, cantidad, importe_unitario, total, a_cuenta, saldo)
        VALUES ('$empresa', '$ruc', '$telefono', '$fecha', '$descripcion', $cantidad, $importe_unitario, $total, $a_cuenta, $saldo)";

if ($conexion->query($sql) === TRUE) {
    // Mensaje de éxito y redirigir automáticamente a registrar.html después de 3 segundos
    echo "<div style='text-align:center; margin-top:50px; font-family: Arial, sans-serif;'>
            <h3>Contrato registrado exitosamente.</h3>
            <p>Serás redirigido para registrar otro contrato...</p>
          </div>
          <script>
            setTimeout(function() {
                window.location.href = '../registrar.html';
            }, 3000);
          </script>";
} else {
    echo "Error: " . $conexion->error;
}

$conexion->close();
?>