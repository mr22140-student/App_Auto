<!-- ========================= -->
<!-- clientes.php -->
<!-- ========================= -->

<?php

include("conexion.php");

$resultado = mysqli_query($conn,"
SELECT * FROM cliente
");

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Clientes</title>

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

<h2 class="mb-4">

Clientes

</h2>

<form action="agregar_cliente.php" method="POST">

<div class="row mb-3">

<div class="col">
<input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
</div>

<div class="col">
<input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
</div>

<div class="col">
<input type="text" name="telefono" class="form-control" placeholder="Teléfono">
</div>

<div class="col">
<input type="email" name="correo" class="form-control" placeholder="Correo">
</div>

</div>

<button class="btn btn-custom mb-4">

Agregar Cliente

</button>

</form>

<?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

<div class="mb-4">

<strong>

<?php echo $fila['nombre']." ".$fila['apellido']; ?>

</strong>

<br>

<?php echo $fila['telefono']; ?> |
<?php echo $fila['correo']; ?>

</div>

<?php } ?>

</div>

</div>

</body>

</html>