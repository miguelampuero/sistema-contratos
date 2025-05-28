<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $empresa = $_POST['nombre_empresa'];
    $ruc = $_POST['ruc'];
    $telefono = $_POST['telefono'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $cantidad = (float)$_POST['cantidad'];
    $importe = (float)$_POST['importe_unitario'];
    $a_cuenta = (float)$_POST['a_cuenta'];
    $estado = $_POST['estado'];

    $total = $cantidad * $importe;
    $saldo = $total - $a_cuenta;

    $sql = "UPDATE contratos SET 
                nombre_empresa = ?, 
                ruc = ?, 
                telefono = ?, 
                fecha = ?, 
                descripcion = ?, 
                cantidad = ?, 
                importe_unitario = ?, 
                total = ?, 
                a_cuenta = ?, 
                saldo = ?, 
                estado = ?
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssdiddssi",
        $empresa,
        $ruc,
        $telefono,
        $fecha,
        $descripcion,
        $cantidad,
        $importe,
        $total,
        $a_cuenta,
        $saldo,
        $estado,
        $id
    );

    if ($stmt->execute()) {
        header("Location: listar.php?edit=success");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Acceso no autorizado.";
}
?>