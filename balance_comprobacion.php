<?php
include("conexion.php");

// Consulta SQL avanzada que une el catálogo con las sumas del libro diario
$query = "
    SELECT 
        c.codigo,
        c.nombre,
        c.tipo,
        IFNULL(SUM(l.debe), 0) AS total_debe,
        IFNULL(SUM(l.haber), 0) AS total_haber
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    GROUP BY c.id
    ORDER BY c.codigo ASC
";

$resultado = mysqli_query($conn, $query);

// Variables para acumular los totales generales del balance
$global_debe = 0;
$global_haber = 0;
$global_deudor = 0;
$global_acreedor = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance de Comprobación - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; }
        .total-row { background-color: #333333 !important; font-weight: bold; }
    </style>
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

    <div class="main container py-4">
        <div class="card-custom">
            <h2 class="text-warning-custom mb-2">Balance de Comprobación</h2>
            <p class="text-white-50">Verificación del principio de partida doble y saldos acumulados por cuenta contable.</p>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th rowspan="2" class="align-middle text-start">Código</th>
                            <th rowspan="2" class="align-middle text-start">Nombre de la Cuenta</th>
                            <th colspan="2">Movimientos</th>
                            <th colspan="2">Saldos</th>
                        </tr>
                        <tr>
                            <th>Debe ($)</th>
                            <th>Haber ($)</th>
                            <th>Deudor ($)</th>
                            <th>Acreedor ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($row = mysqli_fetch_assoc($resultado)){ 
                            $debe = floatval($row['total_debe']);
                            $haber = floatval($row['total_haber']);
                            
                            $deudor = 0;
                            $acreedor = 0;

                            // Determinar el saldo según la naturaleza del tipo de cuenta
                            if (in_array($row['tipo'], ['ACTIVO', 'COSTO', 'GASTO'])) {
                                $saldo = $debe - $haber;
                                if ($saldo >= 0) {
                                    $deudor = $saldo;
                                } else {
                                    $acreedor = abs($saldo); // Si es negativo invierte su naturaleza
                                }
                            } else { // PASIVO, PATRIMONIO, INGRESO
                                $saldo = $haber - $debe;
                                if ($saldo >= 0) {
                                    $acreedor = $saldo;
                                } else {
                                    $deudor = abs($saldo);
                                }
                            }

                            // Sumar a los totales globales
                            $global_debe += $debe;
                            $global_haber += $haber;
                            $global_deudor += $deudor;
                            $global_acreedor += $acreedor;
                            
                            // Omitir visualmente cuentas que no tienen ningún movimiento ni saldo para no saturar la tabla
                            if ($debe == 0 && $haber == 0) continue;
                        ?>
                        <tr>
                            <td><code><?php echo $row['codigo']; ?></code></td>
                            <td><?php echo $row['nombre']; ?> <small class="text-muted">(<?php echo $row['tipo']; ?>)</small></td>
                            <td class="text-end"><?php echo number_format($debe, 2); ?></td>
                            <td class="text-end"><?php echo number_format($haber, 2); ?></td>
                            <td class="text-end text-info"><?php echo $deudor > 0 ? number_format($deudor, 2) : '-'; ?></td>
                            <td class="text-end text-warning"><?php echo $acreedor > 0 ? number_format($acreedor, 2) : '-'; ?></td>
                        </tr>
                        <?php } ?>
                        
                        <tr class="table-dark total-row">
                            <td colspan="2" class="text-start text-uppercase">Totales</td>
                            <td class="text-end text-warning">$<?php echo number_format($global_debe, 2); ?></td>
                            <td class="text-end text-warning">$<?php echo number_format($global_haber, 2); ?></td>
                            <td class="text-end text-success">$<?php echo number_format($global_deudor, 2); ?></td>
                            <td class="text-end text-success">$<?php echo number_format($global_acreedor, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php if (number_format($global_debe, 2) === number_format($global_haber, 2) && number_format($global_deudor, 2) === number_format($global_acreedor, 2)): ?>
                    <div class="alert alert-success bg-success text-white border-0 text-center m-0">
                        ✔ <strong>¡Partida Doble Cuadrada Exitosamente!</strong> Los movimientos y saldos se encuentran en perfecto equilibrio contable.
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger bg-danger text-white border-0 text-center m-0">
                        ⚠ <strong>Desbalance detectado:</strong> Las sumas de los movimientos o saldos no coinciden. Revisa que todas las transacciones comerciales tengan su contrapartida correcta.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>