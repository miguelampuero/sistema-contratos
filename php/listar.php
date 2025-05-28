<?php
require_once "conexion.php";

$busqueda = isset($_GET['buscar']) ? $conexion->real_escape_string(trim($_GET['buscar'])) : '';

// Paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

// Contar total de registros con filtro
$sql_count = "SELECT COUNT(*) as total FROM contratos";
if ($busqueda !== '') {
    $sql_count .= " WHERE nombre_empresa LIKE '%$busqueda%' OR ruc LIKE '%$busqueda%'";
}
$result_count = $conexion->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_registros = (int)$row_count['total'];

// Calcular total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Calcular el OFFSET para SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta principal con LIMIT y OFFSET
$sql = "SELECT * FROM contratos";
if ($busqueda !== '') {
    $sql .= " WHERE nombre_empresa LIKE '%$busqueda%' OR ruc LIKE '%$busqueda%'";
}
$sql .= " ORDER BY id ASC LIMIT $registros_por_pagina OFFSET $offset";

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
    <style>
        table thead tr th:nth-child(5),
        table tbody tr td:nth-child(5) {
            min-width: 120px;
            white-space: nowrap;
        }
        table th, table td {
            vertical-align: middle;
            white-space: nowrap;
        }
        table tbody tr td:nth-child(6),
        table tbody tr td:nth-child(7),
        table tbody tr td:nth-child(8) {
            white-space: normal;
        }
    </style>
</head>
<body>
<div class="sidebar text-center">
  <a href="../index.html"><img src="../img/logo.png" alt="Logo" class="img-fluid" style="max-width: 180px; margin-bottom: 15px;"></a>
  <a href="../index.html"><i class="bi bi-house-door-fill"></i> Inicio</a>
  <a href="../registrar.html"><i class="bi bi-file-earmark-plus-fill"></i> Registrar Contrato</a>
  <a href="listar.php" class="active"><i class="bi bi-card-list"></i> Listar Contratos</a>
</div>

<div class="content container mt-4">
    <h2>Contratos registrados</h2>

    <?php if (isset($_GET['edit']) && $_GET['edit'] === 'success'): ?>
        <div class="alert alert-success">Contrato actualizado correctamente.</div>
    <?php endif; ?>

    <form class="row g-3 mb-3" method="GET" action="listar.php">
        <div class="col-md-4">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por empresa o RUC..." value="<?= htmlspecialchars($busqueda) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
        </div>
    </form>

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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                    <?php
                    $id_contrato = $fila['id'];
                    $sql_items = "SELECT * FROM items_contrato WHERE contrato_id = $id_contrato";
                    $res_items = $conexion->query($sql_items);

                    $descripciones = [];
                    $cantidades = [];
                    $importes = [];
                    if ($res_items && $res_items->num_rows > 0) {
                        while ($item = $res_items->fetch_assoc()) {
                            $descripciones[] = htmlspecialchars($item['descripcion']);
                            $cantidades[] = number_format($item['cantidad'], 2);
                            $importes[] = number_format($item['importe_unitario'], 2);
                        }
                    } else {
                        $descripciones[] = '-';
                        $cantidades[] = '-';
                        $importes[] = '-';
                    }
                    ?>
                    <tr>
                        <td rowspan="1"><?= $fila['id'] ?></td>
                        <td rowspan="1"><?= htmlspecialchars($fila['nombre_empresa']) ?></td>
                        <td rowspan="1"><?= htmlspecialchars($fila['ruc']) ?></td>
                        <td rowspan="1"><?= htmlspecialchars($fila['telefono']) ?></td>
                        <td rowspan="1"><?= $fila['fecha'] ?></td>
                        <td><?= implode("<br>", $descripciones) ?></td>
                        <td><?= implode("<br>", $cantidades) ?></td>
                        <td><?= implode("<br>", $importes) ?></td>
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

        <!-- Paginación -->
        <nav aria-label="Navegación de páginas">
            <ul class="pagination justify-content-center">
                <!-- Botón Anterior -->
                <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?buscar=<?= urlencode($busqueda) ?>&pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
                </li>

                <!-- Números de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link" href="?buscar=<?= urlencode($busqueda) ?>&pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Botón Siguiente -->
                <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?buscar=<?= urlencode($busqueda) ?>&pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>

    <?php else: ?>
        <p>No se encontraron contratos.</p>
    <?php endif; ?>

    <a href="../registrar.html" class="btn btn-primary mt-3">Registrar nuevo contrato</a>
</div>

</body>
</html>

<?php
$conexion->close();
?>
