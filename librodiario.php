<?php
include("conexion.php");
$asientos = mysqli_query($conn, "
    SELECT l.*, c.codigo, c.nombre as cuenta_nombre 
    FROM libro_diario l
    JOIN catalogo_cuentas c ON l.cuenta_id = c.id
    ORDER BY l.fecha DESC, l.id ASC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Diario General</title>
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
            <a href="libromayor.php">Libro Mayor</a>
            <a href="catalogo.php">Catálogo y Manual</a>
            <a href="razones.php">Razones Financieras</a>
            <a href="balance_general.php">Balance General</a>
            <a href="balance_comprobacion.php">Balance Comprobación</a>
            <a href="analisis_financiero.php">Análisis H/V</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom">
            <h2 class="mb-4">Libro Diario de Operaciones</h2>
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Fecha</th>
                        <th>Código Cuenta</th>
                        <th>Concepto / Cuenta</th>
                        <th class="text-end">Debe (Cargo)</th>
                        <th class="text-end">Haber (Abono)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($asientos)){ 
                        $es_abono = ($row['haber'] > 0);
                    ?>
                    <tr>
                        <td><small><?php echo $row['fecha']; ?></small></td>
                        <td><code><?php echo $row['codigo']; ?></code></td>
                        <td style="padding-left: <?php echo $es_abono ? '40px' : '12px'; ?>;">
                            <?php echo $row['cuenta_nombre']; ?>
                            <br><small class="text-muted"><?php echo $row['descripcion']; ?></small>
                        </td>
                        <td class="text-end text-success"><?php echo $row['debe'] > 0 ? '$'.number_format($row['debe'], 2) : '-'; ?></td>
                        <td class="text-end text-danger"><?php echo $row['haber'] > 0 ? '$'.number_format($row['haber'], 2) : '-'; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>