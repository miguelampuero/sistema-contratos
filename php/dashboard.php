<?php 
require_once "conexion.php";

$sql_dashboard = "
SELECT
  YEAR(fecha) AS anio,
  MONTH(fecha) AS mes,
  COUNT(*) AS total_contratos,
  SUM(total) AS suma_total,
  SUM(a_cuenta) AS suma_acuenta,
  SUM(saldo) AS suma_saldo
FROM contratos
GROUP BY anio, mes
ORDER BY anio DESC, mes DESC
";

$result = $conexion->query($sql_dashboard);

function nombreMes($num) {
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    return $meses[$num - 1] ?? '';
}

// Preparar datos para gráficos
$labels = [];
$dataContratos = [];
$dataFacturado = [];
$dataAcuenta = [];
$dataSaldo = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = nombreMes($row['mes']) . ' ' . $row['anio'];
        $dataContratos[] = (int)$row['total_contratos'];
        $dataFacturado[] = (float)$row['suma_total'];
        $dataAcuenta[] = (float)$row['suma_acuenta'];
        $dataSaldo[] = (float)$row['suma_saldo'];
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Mensual - Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar text-center">
  <a href="../index.html">
    <img src="../img/logo.png" alt="Logo" class="img-fluid" style="max-width: 180px; margin-bottom: 15px;">
  </a>
  <a href="../index.html"><i class="bi bi-house-door-fill"></i> Inicio</a>
  <a href="../registrar.html"><i class="bi bi-file-earmark-plus-fill"></i> Registrar Contrato</a>
  <a href="listar.php"><i class="bi bi-card-list"></i> Listar Contratos</a>
  <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
</div>

<!-- Contenido principal -->
<div class="main-content">
  <div class="container mt-4">
    <h2 class="mb-4">Dashboard mensual de contratos</h2>

    <!-- Tabla -->
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Mes - Año</th>
                <th>Total Contratos</th>
                <th>Total Facturado</th>
                <th>Total A Cuenta</th>
                <th>Saldo Pendiente</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($labels)): ?>
                <?php for ($i = 0; $i < count($labels); $i++): ?>
                <tr>
                    <td><?= $labels[$i] ?></td>
                    <td><?= $dataContratos[$i] ?></td>
                    <td><?= number_format($dataFacturado[$i], 2) ?></td>
                    <td><?= number_format($dataAcuenta[$i], 2) ?></td>
                    <td><?= number_format($dataSaldo[$i], 2) ?></td>
                </tr>
                <?php endfor; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No hay datos para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Gráfico: Total Contratos -->
    <div class="my-4">
        <h5 class="text-center">Total de Contratos por Mes</h5>
        <canvas id="contratosChart" style="max-height: 300px;"></canvas>
    </div>

    <!-- Gráfico: Montos -->
    <div class="my-4">
        <h5 class="text-center">Montos por Mes</h5>
        <canvas id="montosChart" style="max-height: 300px;"></canvas>
    </div>
  </div>
</div>

<!-- Scripts Chart.js -->
<script>
// Gráfico de Contratos
const ctxContratos = document.getElementById('contratosChart').getContext('2d');
new Chart(ctxContratos, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Total Contratos',
            data: <?= json_encode($dataContratos) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Gráfico de Montos
const ctxMontos = document.getElementById('montosChart').getContext('2d');
new Chart(ctxMontos, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Facturado',
                data: <?= json_encode($dataFacturado) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'A Cuenta',
                data: <?= json_encode($dataAcuenta) ?>,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Saldo Pendiente',
                data: <?= json_encode($dataSaldo) ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            legend: { display: true }
        }
    }
});
</script>
</body>
</html>