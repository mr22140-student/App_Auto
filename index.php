<?php
// 1. Incluimos la conexión a la base de datos para jalar los clientes
include("conexion.php");

// 2. Traemos la consulta de los clientes
$resultado_clientes = mysqli_query($conn, "SELECT * FROM cliente");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - ERP Auto Repuestos</title>
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
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom text-center py-5 mb-4">
            <h1 class="display-4 text-secondary mb-3">Bienvenido al ERP Auto Repuestos</h1>
            <p class="lead text-muted">Sistema integrado de inventario, facturación multimétodo y contabilidad automatizada por partida doble.</p>
            <hr class="my-4" style="max-width: 400px; margin: auto;">
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="ventas.php" class="btn btn-custom px-4">Registrar Venta</a>
                <a href="compras.php" class="btn btn-secondary px-4">Registrar Compra</a>
                <a href="librodiario.php" class="btn btn-dark px-4">Ver Libro Diario</a>
            </div>
        </div>

        <div class="table-container">
            <h3 class="mb-4 text-warning">Nuestros Clientes Registrados</h3>

            <?php 
            // Validamos si hay clientes en la base de datos para evitar errores
            if(mysqli_num_rows($resultado_clientes) > 0) {
                while($fila = mysqli_fetch_assoc($resultado_clientes)){ 
            ?>
                <div class="mb-4 border-bottom border-secondary pb-3">
                    <strong>
                        <?php echo $fila['nombre']." ".$fila['apellido']; ?>
                    </strong>
                    <br>
                    <span class="text-white-50">
                        <?php echo !empty($fila['telefono']) ? $fila['telefono'] : 'Sin teléfono'; ?> | 
                        <?php echo !empty($fila['correo']) ? $fila['correo'] : 'Sin correo'; ?>
                    </span>
                </div>
            <?php 
                } 
            } else {
                echo "<p class='text-muted'>No hay clientes registrados en el sistema todavía.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>