<?php
// 1. Conexión a la base de datos
include("conexion.php");

// 2. Consultas para obtener los totales reales
$query_ventas = mysqli_query($conn, "SELECT SUM(total) as total FROM venta");
$data_ventas = mysqli_fetch_assoc($query_ventas);
$total_ventas = isset($data_ventas['total']) ? floatval($data_ventas['total']) : 0.00;

$query_compras = mysqli_query($conn, "SELECT SUM(total) as total FROM compra");
$data_compras = mysqli_fetch_assoc($query_compras);
$total_compras = isset($data_compras['total']) ? floatval($data_compras['total']) : 0.00;

// 3. Cálculo de la Utilidad Neta (Ingresos - Costos)
$utilidad_neta = $total_ventas - $total_compras;

// 4. Calcular porcentaje de costo (Evitar división por cero)
$porcentaje_costo = ($total_ventas > 0) ? round(($total_compras / $total_ventas) * 100) : 0;
if($porcentaje_costo > 100) $porcentaje_costo = 100;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Financieros - ERP Auto Repuestos</title>
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
            <a href="balance_general.php">Balance General</a>
            <a href="balance_comprobacion.php">Balance Comprobación</a>
            <a href="analisis_financiero.php">Análisis H/V</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main">
        
        <div class="table-container mb-4">
            <h2 class="text-white mb-2 fw-semibold">Dashboard de Reportes Financieros</h2>
            <p class="text-white-50 m-0">Análisis del rendimiento comercial, utilidades brutas y balance operativo de la empresa.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card-custom p-4 border-start border-warning border-4 h-100" style="background-color: #1a1a1a;">
                    <span class="text-white-50 text-uppercase small fw-bold" style="letter-spacing: 0.5px;">Ingresos Totales (Ventas)</span>
                    <h2 class="text-warning mt-2 fw-bold">$<?php echo number_format($total_ventas, 2); ?></h2>
                    <small class="text-white-50">Reflejado en Caja y Bancos</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 border-start border-secondary border-4 h-100" style="background-color: #1a1a1a;">
                    <span class="text-white-50 text-uppercase small fw-bold" style="letter-spacing: 0.5px;">Costos Operativos (Compras)</span>
                    <h2 class="text-white mt-2 fw-bold">$<?php echo number_format($total_compras, 2); ?></h2>
                    <small class="text-white-50">Inversión en inventario de repuestos</small>
                </div>
            </div>

            <div class="col-md-4">
                <?php if ($utilidad_neta >= 0): ?>
                    <div class="card-custom p-4 border-start border-success border-4 h-100" style="background-color: #1a1a1a;">
                        <span class="text-white-50 text-uppercase small fw-bold" style="letter-spacing: 0.5px;">Utilidad Neta del Ejercicio</span>
                        <h2 class="text-success mt-2 fw-bold">$<?php echo number_format($utilidad_neta, 2); ?></h2>
                        <small class="text-success" style="opacity: 0.8;">✔ El negocio está generando ganancias</small>
                    </div>
                <?php else: ?>
                    <div class="card-custom p-4 border-start border-danger border-4 h-100" style="background-color: #1a1a1a;">
                        <span class="text-white-50 text-uppercase small fw-bold" style="letter-spacing: 0.5px;">Pérdida Neta del Ejercicio</span>
                        <h2 class="text-danger mt-2 fw-bold">$<?php echo number_format(abs($utilidad_neta), 2); ?></h2>
                        <small class="text-danger" style="opacity: 0.8;">⚠ Los costos superan los ingresos actuales</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="table-container h-100">
                    <h4 class="text-white mb-3">Eficiencia de Costos</h4>
                    <p class="text-white-50 small">Este indicador muestra qué porcentaje de tus ingresos se está consumiendo en compras de inventario.</p>
                    
                    <div class="progress bg-dark mt-4" style="height: 25px; border-radius: 6px;">
                        <div class="progress-bar bg-warning text-dark fw-bold" role="progressbar" 
                             style="width: <?php echo 100 - $porcentaje_costo; ?>%;" 
                             aria-valuenow="<?php echo 100 - $porcentaje_costo; ?>" aria-valuemin="0" aria-valuemax="100">
                             Margen Libre (<?php echo 100 - $porcentaje_costo; ?>%)
                        </div>
                        <div class="progress-bar bg-danger fw-bold" role="progressbar" 
                             style="width: <?php echo $porcentaje_costo; ?>%;" 
                             aria-valuenow="<?php echo $porcentaje_costo; ?>" aria-valuemin="0" aria-valuemax="100">
                             Costo (<?php echo $porcentaje_costo; ?>%)
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 text-white-50 small">
                        <span>Ingresos Disponibles</span>
                        <span>Costo de lo vendido</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="table-container h-100">
                    <h4 class="text-warning mb-3">Herramientas de Auditoría</h4>
                    <div class="d-grid gap-2 mt-2">
                        <a href="balance_comprobacion.php" class="btn btn-dark text-start border-secondary py-2">
                            📊 Balance de Comprobación <span class="float-end">➔</span>
                        </a>
                        <a href="balance_general.php" class="btn btn-dark text-start border-secondary py-2">
                            🏛️ Balance General Proyectado <span class="float-end">➔</span>
                        </a>
                        <a href="razones.php" class="btn btn-dark text-start border-secondary py-2">
                            📈 Índices y Razones de Liquidez <span class="float-end">➔</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>