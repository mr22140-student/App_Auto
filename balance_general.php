<?php
include("conexion.php");

// 1. CONSULTAR LOS SALDOS DE LOS TRES GRANDES GRUPOS (Clase 1, 2 y 3 del catálogo)
// Nota: Ajusta los comodines LIKE según la estructura exacta de tus códigos de cuenta.

// --- GRUPO 1: ACTIVOS ---
$activos_query = mysqli_query($conn, "
    SELECT c.codigo, c.nombre, 
           (IFNULL(SUM(l.debe), 0) - IFNULL(SUM(l.haber), 0)) AS saldo
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    WHERE c.codigo LIKE '1%' 
    GROUP BY c.id
    HAVING saldo != 0
    ORDER BY c.codigo ASC
");

// --- GRUPO 2: PASIVOS ---
$pasivos_query = mysqli_query($conn, "
    SELECT c.codigo, c.nombre, 
           (IFNULL(SUM(l.haber), 0) - IFNULL(SUM(l.debe), 0)) AS saldo
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    WHERE c.codigo LIKE '2%' 
    GROUP BY c.id
    HAVING saldo != 0
    ORDER BY c.codigo ASC
");

// --- GRUPO 3: PATRIMONIO ---
$patrimonio_query = mysqli_query($conn, "
    SELECT c.codigo, c.nombre, 
           (IFNULL(SUM(l.haber), 0) - IFNULL(SUM(l.debe), 0)) AS saldo
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    WHERE c.codigo LIKE '3%' 
    GROUP BY c.id
    HAVING saldo != 0
    ORDER BY c.codigo ASC
");

// 2. TOTALIZADORES EN CERO
$total_activos = 0;
$total_pasivos = 0;
$total_patrimonio = 0;

// Arrays para guardar temporalmente los datos y poder calcular los totales antes de renderizar
$lista_activos = mysqli_fetch_all($activos_query, MYSQLI_ASSOC);
$lista_pasivos = mysqli_fetch_all($pasivos_query, MYSQLI_ASSOC);
$lista_patrimonio = mysqli_fetch_all($patrimonio_query, MYSQLI_ASSOC);

foreach ($lista_activos as $a) { $total_activos += floatval($a['saldo']); }
foreach ($lista_pasivos as $p) { $total_pasivos += floatval($p['saldo']); }
foreach ($lista_patrimonio as $pat) { $total_patrimonio += floatval($pat['saldo']); }

$total_pasivo_patrimonio = $total_pasivos + $total_patrimonio;

// 3. VERIFICAR CUADRE (Margen de tolerancia por decimales de punto flotante)
$esta_cuadrado = abs($total_activos - $total_pasivo_patrimonio) < 0.01;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance General - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-balance { color: #ffffff; margin-bottom: 0; }
        .table-balance th { background-color: #1f1f1f !important; color: #ffc107; border-bottom: 2px solid #444; }
        .table-balance td { border-bottom: 1px solid #333; }
        .total-row { background-color: #2b2b2b; font-weight: bold; color: #ffc107; }
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
            <a href="libromayor.php">Libro Mayor</a>
            <a href="catalogo.php">Catálogo y Manual</a>
            <a href="razones.php">Razones Financieras</a>
            <a href="balance_general.php">Balance General</a>
            <a href="balance_comprobacion.php">Balance Comprobación</a>
            <a href="analisis_financiero.php">Análisis H/V</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main container py-4">
        <div class="card-custom text-center">
            <h2 class="text-warning-custom mb-1">ERP Auto Repuestos S.A. de C.V.</h2>
            <h4 class="text-white-50 mb-2">Balance General</h4>
            <p class="small text-muted m-0">Expresado en Dólares ($) - Al: <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <div class="alert <?php echo $esta_cuadrado ? 'alert-success bg-success' : 'alert-danger bg-danger'; ?> text-white border-0 text-center py-2 mb-4">
            <?php 
                echo $esta_cuadrado 
                    ? "✔ <strong>Estado Financiero Cuadrado:</strong> Activo es exactamente igual a la suma de Pasivo y Patrimonio." 
                    : "⚠ <strong>Aviso de Descuadre:</strong> Existe una diferencia de $" . number_format(abs($total_activos - $total_pasivo_patrimonio), 2) . " entre los rubros de la ecuación."; 
            ?>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h4 class="text-white mb-3">1. Activos</h4>
                    <div class="table-responsive">
                        <table class="table table-balance align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cuenta / Concepto</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($lista_activos)): ?>
                                    <tr><td colspan="3" class="text-muted text-center">No hay registros de activo.</td></tr>
                                <?php else: ?>
                                    <?php foreach($lista_activos as $act): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($act['codigo'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                        <td><?php echo htmlspecialchars($act['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end <?php echo $act['saldo'] < 0 ? 'text-danger' : ''; ?>">
                                            $<?php echo number_format(floatval($act['saldo']), 2); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td colspan="2">TOTAL ACTIVOS</td>
                                    <td class="text-end">$<?php echo number_format($total_activos, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-custom mb-4">
                    <h4 class="text-white mb-3">2. Pasivos</h4>
                    <div class="table-responsive">
                        <table class="table table-balance align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cuenta / Concepto</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($lista_pasivos)): ?>
                                    <tr><td colspan="3" class="text-muted text-center">No hay registros de pasivo.</td></tr>
                                <?php else: ?>
                                    <?php foreach($lista_pasivos as $pas): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($pas['codigo'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                        <td><?php echo htmlspecialchars($pas['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end">$<?php echo number_format(floatval($pas['saldo']), 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td colspan="2">TOTAL PASIVOS</td>
                                    <td class="text-end">$<?php echo number_format($total_pasivos, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-custom">
                    <h4 class="text-white mb-3">3. Patrimonio</h4>
                    <div class="table-responsive">
                        <table class="table table-balance align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cuenta / Concepto</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($lista_patrimonio)): ?>
                                    <tr><td colspan="3" class="text-muted text-center">No hay registros de patrimonio.</td></tr>
                                <?php else: ?>
                                    <?php foreach($lista_patrimonio as $patr): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($patr['codigo'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                        <td><?php echo htmlspecialchars($patr['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end">$<?php echo number_format(floatval($patr['saldo']), 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td colspan="2">TOTAL PATRIMONIO</td>
                                    <td class="text-end">$<?php echo number_format($total_patrimonio, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-3 mt-4 text-center rounded bg-dark border border-warning">
                    <h5 class="m-0 text-white-50">TOTAL PASIVO + PATRIMONIO</h5>
                    <h3 class="m-0 text-warning font-weight-bold mt-1">$<?php echo number_format($total_pasivo_patrimonio, 2); ?></h3>
                </div>
            </div>
        </div>
    </div>
</body>
</html>