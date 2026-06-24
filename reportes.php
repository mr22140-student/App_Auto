<!-- ========================= -->
<!-- reportes.php -->
<!-- ========================= -->

<?php

include("conexion.php");

$ventas = mysqli_query($conn,"
SELECT SUM(total) AS total_ventas
FROM venta
");

$compras = mysqli_query($conn,"
SELECT SUM(total) AS total_compras
FROM compra
");

$totalVentas = mysqli_fetch_assoc($ventas);
$totalCompras = mysqli_fetch_assoc($compras);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Reportes</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="navbar-custom">
        <span class="navbar-title">ERP Auto Repuestos</span>
        <div>
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="clientes.php">Clientes</a>
            <a href="ventas.php">Ventas</a>
            <a href="compras.php">Compras</a>
            <a href="librodiario.php">Libro Diario</a>
            <a href="catalogo.php">Catálogo y Manual</a>
            <a href="razones.php">Razones Financieras</a>
            <a href="balance_comprobacion.php">Balance Comprobación</a>
            <a href="analisis_financiero.php">Análisis H/V</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

<div class="main">

<div class="table-container">

<h2 class="mb-5">

Reportes Financieros

</h2>

<div class="row">

<div class="col-md-6">

<div class="table-container">

<h4>

Total Ventas

</h4>

<h2 class="text-warning">

$<?php echo number_format($totalVentas['total_ventas'],2); ?>

</h2>

</div>

</div>

<div class="col-md-6">

<div class="table-container">

<h4>

Total Compras

</h4>

<h2 class="text-warning">

$<?php echo number_format($totalCompras['total_compras'],2); ?>

</h2>

</div>

</div>

</div>

</div>

</div>

</body>

</html>