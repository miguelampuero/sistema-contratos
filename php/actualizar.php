<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'], $_POST['nombre_empresa'], $_POST['ruc'], $_POST['fecha'], $_POST['estado'], $_POST['items'])) {
        die("Faltan datos obligatorios.");
    }

    $id = (int)$_POST['id'];
    $empresa = trim($_POST['nombre_empresa']);
    $ruc = trim($_POST['ruc']);
    $telefono = trim($_POST['telefono'] ?? '');
    $fecha = $_POST['fecha'];
    $a_cuenta = isset($_POST['a_cuenta']) ? (float)$_POST['a_cuenta'] : 0.0;
    $estado = $_POST['estado'];

    if (!in_array($estado, ['Proceso', 'Finalizado'])) {
        die("Estado inválido.");
    }

    $total = 0.0;

    if (!is_array($_POST['items']) || count($_POST['items']) === 0) {
        die("Debe haber al menos un ítem.");
    }

    $conexion->autocommit(false);

    try {
        // Actualizar contrato
        $sql_contrato = "UPDATE contratos SET 
            nombre_empresa = ?, 
            ruc = ?, 
            telefono = ?, 
            fecha = ?, 
            a_cuenta = ?, 
            estado = ? 
            WHERE id = ?";
        $stmt_contrato = $conexion->prepare($sql_contrato);

        // Corregir tipos: s = string, d = double, i = int
        // empresa(s), ruc(s), telefono(s), fecha(s), a_cuenta(d), estado(s), id(i)
        $stmt_contrato->bind_param("ssssdsi", $empresa, $ruc, $telefono, $fecha, $a_cuenta, $estado, $id);

        if (!$stmt_contrato->execute()) {
            throw new Exception("Error al actualizar contrato: " . $stmt_contrato->error);
        }
        $stmt_contrato->close();

        // Actualizar ítems
        $sql_item = "UPDATE items_contrato SET descripcion = ?, cantidad = ?, importe_unitario = ? WHERE id = ? AND contrato_id = ?";
        $stmt_item = $conexion->prepare($sql_item);

        foreach ($_POST['items'] as $item) {
            if (!isset($item['id'], $item['descripcion'], $item['cantidad'], $item['importe_unitario'])) {
                throw new Exception("Datos incompletos en ítems.");
            }
            $item_id = (int)$item['id'];
            $descripcion = trim($item['descripcion']);
            $cantidad = (float)$item['cantidad'];
            $importe_unitario = (float)$item['importe_unitario'];

            if ($cantidad < 0 || $importe_unitario < 0) {
                throw new Exception("Cantidad e importe unitario deben ser positivos.");
            }

            $total += $cantidad * $importe_unitario;

            $stmt_item->bind_param("sddii", $descripcion, $cantidad, $importe_unitario, $item_id, $id);
            if (!$stmt_item->execute()) {
                throw new Exception("Error al actualizar ítem: " . $stmt_item->error);
            }
        }
        $stmt_item->close();

        // Actualizar total y saldo
        $saldo = $total - $a_cuenta;
        $sql_totales = "UPDATE contratos SET total = ?, saldo = ? WHERE id = ?";
        $stmt_totales = $conexion->prepare($sql_totales);

        // Corregir tipos: total(d), saldo(d), id(i)
        $stmt_totales->bind_param("ddi", $total, $saldo, $id);

        if (!$stmt_totales->execute()) {
            throw new Exception("Error al actualizar totales: " . $stmt_totales->error);
        }
        $stmt_totales->close();

        $conexion->commit();

        header("Location: listar.php?edit=success");
        exit();

    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error en la actualización: " . $e->getMessage();
    }

    $conexion->close();

} else {
    echo "Acceso no autorizado.";
}
?>