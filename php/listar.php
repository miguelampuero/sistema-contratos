<?php
require_once "conexion.php";

$sql = "SELECT * FROM contratos ORDER BY id DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Listar Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilo.css" />
</head>
<body>
<div class="sidebar text-center">
  <a href="../index.html">
    <img src="../img/logo.png" alt="Logo" class="img-fluid" style="max-width: 180px; margin-bottom: 15px;">
  </a>
  <a href="../index.html"><i class="bi bi-house-door-fill"></i> Inicio</a>
  <a href="../registrar.html"><i class="bi bi-file-earmark-plus-fill"></i> Registrar Contrato</a>
  <a href="listar.php" class="active"><i class="bi bi-card-list"></i> Listar Contratos</a>
</div>

<div class="content container mt-4">
    <h2>Contratos registrados</h2>
    <?php if (isset($_GET['edit']) && $_GET['edit'] === 'success'): ?>
    <div class="alert alert-success">Contrato actualizado correctamente.</div>
<?php endif; ?>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Señor(es)</th>
                    <th>R.U.C.</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Importe Unitario</th>
                    <th>Total</th>
                    <th>A cuenta</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acciones</th> <!-- Nueva columna -->
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id'] ?></td>
                    <td><?= htmlspecialchars($fila['nombre_empresa']) ?></td>
                    <td><?= htmlspecialchars($fila['ruc']) ?></td>
                    <td><?= htmlspecialchars($fila['telefono']) ?></td>
                    <td><?= $fila['fecha'] ?></td>
                    <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                    <td><?= number_format($fila['cantidad'], 2) ?></td>
                    <td><?= number_format($fila['importe_unitario'], 2) ?></td>
                    <td><?= number_format($fila['total'], 2) ?></td>
                    <td><?= number_format($fila['a_cuenta'], 2) ?></td>
                    <td><?= number_format($fila['saldo'], 2) ?></td>
                    <td><?= htmlspecialchars($fila['estado']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Editar
                        </a>
                        <a href="generar_pdf.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm" target="_blank">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>                 
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay contratos registrados.</p>
    <?php endif; ?>

    <a href="../registrar.html" class="btn btn-primary mt-3">Registrar nuevo contrato</a>
</div>

</body>
</html>

<?php
$conexion->close();
?>