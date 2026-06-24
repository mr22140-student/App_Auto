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
            <a href="ventas.php">Ventas</a>
            <a href="compras.php">Compras</a>
            <a href="librodiario.php">Libro Diario</a>
            <a href="catalogo.php">Catálogo y Manual</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom text-center py-5">
            <h1 class="display-4 text-secondary mb-3">Bienvenido al ERP Auto Repuestos</h1>
            <p class="lead text-muted">Sistema integrado de inventario, facturación multimétodo y contabilidad automatizada por partida doble.</p>
            <hr class="my-4" style="max-width: 400px; margin: auto;">
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="ventas.php" class="btn btn-custom px-4">Registrar Venta</a>
                <a href="compras.php" class="btn btn-secondary px-4">Registrar Compra</a>
                <a href="librodiario.php" class="btn btn-dark px-4">Ver Libro Diario</a>
            </div>
        </div>
    </div>
</body>
</html>