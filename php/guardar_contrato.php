<?php
require_once "conexion.php";

// Sanear y obtener datos generales
$empresa = $conexion->real_escape_string($_POST['nombre_empresa']);
$ruc = $conexion->real_escape_string($_POST['ruc']);
$telefono = $conexion->real_escape_string($_POST['telefono']);
$fecha = $conexion->real_escape_string($_POST['fecha']);
$total = floatval($_POST['total']);
$a_cuenta = floatval($_POST['a_cuenta']);
$saldo = floatval($_POST['saldo']);
$estado = 'Proceso'; // Por defecto

// Iniciar transacción para asegurar integridad
$conexion->begin_transaction();

try {
    // Insertar contrato
    $sqlContrato = "INSERT INTO contratos (nombre_empresa, ruc, telefono, fecha, total, a_cuenta, saldo, estado) 
                    VALUES ('$empresa', '$ruc', '$telefono', '$fecha', $total, $a_cuenta, $saldo, '$estado')";

    if (!$conexion->query($sqlContrato)) {
        throw new Exception("Error al insertar contrato: " . $conexion->error);
    }

    $contrato_id = $conexion->insert_id;

    // Insertar ítems
    $descripciones = $_POST['descripcion'];
    $cantidades = $_POST['cantidad'];
    $importes_unitarios = $_POST['importe_unitario'];

    $stmt = $conexion->prepare("INSERT INTO items_contrato (contrato_id, cantidad, descripcion, importe_unitario) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error en preparación de statement: " . $conexion->error);
    }

    for ($i = 0; $i < count($descripciones); $i++) {
        $desc = $descripciones[$i];
        $cant = intval($cantidades[$i]);
        $imp = floatval($importes_unitarios[$i]);

        // Validar datos mínimos
        if (empty($desc) || $cant <= 0 || $imp < 0) {
            continue; // O lanzar error según preferencia
        }

        $stmt->bind_param("iisd", $contrato_id, $cant, $desc, $imp);
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar ítem: " . $stmt->error);
        }
    }

    $stmt->close();

    // Confirmar la transacción
    $conexion->commit();

    // Redireccionar con mensaje éxito
    header("Location: ../registrar.html?mensaje=Contrato registrado correctamente");
    exit();

} catch (Exception $e) {
    $conexion->rollback();
    echo "Error al guardar contrato: " . $e->getMessage();
}

$conexion->close();
?>