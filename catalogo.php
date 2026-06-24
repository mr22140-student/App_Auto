<?php
include("conexion.php");
$cuentas = mysqli_query($conn, "SELECT * FROM catalogo_cuentas ORDER BY codigo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo y Manual de Cuentas</title>
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
            <a href="compras.php">Compras</a> <a href="librodiario.php">Libro Diario</a>
            <a href="catalogo.php">Catálogo y Manual</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom">
            <h2 class="mb-2">Catálogo y Manual de Cuentas</h2>
            <p class="text-muted mb-4">Estructura financiera y reglas operativas aplicadas a los flujos de caja, tarjetas e inventario del ERP.</p>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Cuenta Contable</th>
                            <th>Tipo</th>
                            <th>Manual de Operación / Descripción Dinámica</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = mysqli_fetch_assoc($cuentas)){ ?>
                        <tr>
                            <td><code><?php echo $c['codigo']; ?></code></td>
                            <td><strong><?php echo $c['nombre']; ?></strong></td>
                            <td><span class="badge <?php echo ($c['tipo']=='ACTIVO') ? 'bg-success' : 'bg-primary'; ?>"><?php echo $c['tipo']; ?></span></td>
                            <td><small class="text-secondary"><?php echo $c['descripcion']; ?></small></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>