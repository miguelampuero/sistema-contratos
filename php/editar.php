<?php 
include('conexion.php');

// Validar que el ID esté presente y sea numérico
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID de contrato no válido.");
}

// Consultar el contrato
$sql = "SELECT * FROM contratos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$contrato = $resultado->fetch_assoc();

if (!$contrato) {
    die("Contrato no encontrado.");
}
$stmt->close();

// Consultar los ítems asociados al contrato
$sql_items = "SELECT * FROM items_contrato WHERE contrato_id = ?";
$stmt_items = $conexion->prepare($sql_items);
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$resultado_items = $stmt_items->get_result();
$items = [];
while ($fila = $resultado_items->fetch_assoc()) {
    $items[] = $fila;
}
$stmt_items->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Contrato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="container mt-5">
  <h2>Editar Contrato</h2>
  <form action="actualizar.php" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($contrato['id']) ?>">

    <div class="mb-3">
      <label>Señor(es)</label>
      <input type="text" name="nombre_empresa" class="form-control" value="<?= htmlspecialchars($contrato['nombre_empresa']) ?>" required>
    </div>

    <div class="mb-3">
      <label>RUC</label>
      <input type="text" name="ruc" class="form-control" value="<?= htmlspecialchars($contrato['ruc']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($contrato['telefono']) ?>">
    </div>

    <div class="mb-3">
      <label>Fecha</label>
      <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($contrato['fecha']) ?>" required>
    </div>

    <h4>Ítems del Contrato</h4>
    <div id="items-container">
      <?php foreach($items as $index => $item): ?>
        <div class="card mb-3 p-3 border">
          <input type="hidden" name="items[<?= $index ?>][id]" value="<?= $item['id'] ?>">
          <div class="mb-2">
            <label>Descripción</label>
            <textarea name="items[<?= $index ?>][descripcion]" class="form-control" required><?= htmlspecialchars($item['descripcion']) ?></textarea>
          </div>
          <div class="mb-2">
            <label>Cantidad</label>
            <input type="number" name="items[<?= $index ?>][cantidad]" class="form-control" step="0.01" value="<?= htmlspecialchars($item['cantidad']) ?>" required>
          </div>
          <div class="mb-2">
            <label>Importe Unitario</label>
            <input type="number" name="items[<?= $index ?>][importe_unitario]" class="form-control" step="0.01" value="<?= htmlspecialchars($item['importe_unitario']) ?>" required>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Aquí podrías agregar un botón para agregar más ítems con JavaScript si quieres -->

    <div class="mb-3">
      <label>A cuenta</label>
      <input type="number" name="a_cuenta" step="0.01" class="form-control" value="<?= htmlspecialchars($contrato['a_cuenta']) ?>">
    </div>

    <div class="mb-3">
      <label>Estado</label>
      <select name="estado" class="form-control" required>
        <option value="Proceso" <?= $contrato['estado'] == 'Proceso' ? 'selected' : '' ?>>Proceso</option>
        <option value="Finalizado" <?= $contrato['estado'] == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>