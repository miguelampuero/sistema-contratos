<?php
include('conexion.php');

$id = $_GET['id'];
$sql = "SELECT * FROM contratos WHERE id = $id";
$resultado = $conexion->query($sql);
$contrato = $resultado->fetch_assoc();
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
    <input type="hidden" name="id" value="<?= $contrato['id'] ?>">

    <div class="mb-3">
      <label>Señor(es)</label>
      <input type="text" name="nombre_empresa" class="form-control" value="<?= $contrato['nombre_empresa'] ?>" required>
    </div>

    <div class="mb-3">
      <label>RUC</label>
      <input type="text" name="ruc" class="form-control" value="<?= $contrato['ruc'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= $contrato['telefono'] ?>">
    </div>

    <div class="mb-3">
      <label>Fecha</label>
      <input type="date" name="fecha" class="form-control" value="<?= $contrato['fecha'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Descripción</label>
      <textarea name="descripcion" class="form-control"><?= $contrato['descripcion'] ?></textarea>
    </div>

    <div class="mb-3">
      <label>Cantidad</label>
      <input type="number" name="cantidad" class="form-control" value="<?= $contrato['cantidad'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Importe Unitario</label>
      <input type="number" name="importe_unitario" step="0.01" class="form-control" value="<?= $contrato['importe_unitario'] ?>" required>
    </div>

    <div class="mb-3">
      <label>A cuenta</label>
      <input type="number" name="a_cuenta" step="0.01" class="form-control" value="<?= $contrato['a_cuenta'] ?>">
    </div>

    <div class="mb-3">
      <label>Estado</label>
      <select name="estado" class="form-control">
        <option value="Proceso" <?= $contrato['estado'] == 'Proceso' ? 'selected' : '' ?>>Proceso</option>
        <option value="Finalizado" <?= $contrato['estado'] == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>