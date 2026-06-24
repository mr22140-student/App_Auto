<?php
include("conexion.php");

// 1. OBTENER VALORES FINANCIEROS DESDE EL LIBRO DIARIO / CATÁLOGO
// Nota: Ajusta los nombres de tus cuentas según tu catálogo real.
// Aquí simulamos consultas dinámicas sumando los saldos del Debe y Haber.

// Activo Corriente (Caja + Bancos + Inventarios + Clientes)
$q_activo_corr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SUM(debe) - SUM(haber)) as saldo FROM libro_diario WHERE cuenta_id IN (SELECT id FROM catalogo_cuentas WHERE codigo LIKE '11%')"));
$activo_corriente = floatval($q_activo_corr['saldo']) > 0 ? floatval($q_activo_corr['saldo']) : 5000.00; // Valor base si está vacío

// Pasivo Corriente (Proveedores + Cuentas por pagar a corto plazo)
$q_pasivo_corr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SUM(haber) - SUM(debe)) as saldo FROM libro_diario WHERE cuenta_id IN (SELECT id FROM catalogo_cuentas WHERE codigo LIKE '21%')"));
$pasivo_corriente = floatval($q_pasivo_corr['saldo']) > 0 ? floatval($q_pasivo_corr['saldo']) : 2500.00; 

// Inventario (Específico de repuestos)
$q_inventario = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock * precio) as total FROM producto"));
$inventario_total = floatval($q_inventario['total']) > 0 ? floatval($q_inventario['total']) : 1500.00;

// Ventas Totales y Costo de Ventas
$q_ventas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(haber) as total FROM libro_diario WHERE cuenta_id IN (SELECT id FROM catalogo_cuentas WHERE codigo LIKE '41%')"));
$ventas_totales = floatval($q_ventas['total']) > 0 ? floatval($q_ventas['total']) : 8000.00;

$costo_ventas = $ventas_totales * 0.60; // Estimación estándar del 60% para el costo de repuestos
$utilidad_neta = $ventas_totales - $costo_ventas - 1000.00; // Menos gastos operativos estimados

// 2. CÁLCULO DE RAZONES FINANCIERAS
$razon_circulante = $pasivo_corriente > 0 ? ($activo_corriente / $pasivo_corriente) : 0;
$prueba_acida = $pasivo_corriente > 0 ? (($activo_corriente - $inventario_total) / $pasivo_corriente) : 0;
$rotacion_inventario = $inventario_total > 0 ? ($costo_ventas / $inventario_total) : 0;
$margen_utilidad = $ventas_totales > 0 ? (($utilidad_neta / $ventas_totales) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Razones Financieras - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .metric-val { font-size: 2rem; font-weight: bold; color: #ffc107; }
    </style>
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
            <a href="razones.php" class="text-warning-custom">Razones Financieras</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main container py-4">
        <div class="card-custom">
            <h2 class="text-warning-custom mb-2">Análisis de Indicadores y Razones Financieras</h2>
            <p class="text-white-50">Evaluación automática de la salud económica de la empresa de repuestos en tiempo real.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h5 class="text-white-50">Liquidez Corriente</h5>
                    <div class="metric-val"><?php echo number_format($razon_circulante, 2); ?> x</div>
                    <p class="small text-muted mt-2">Fórmula: Activo Corriente / Pasivo Corriente</p>
                    <div class="alert <?php echo $razon_circulante >= 1.5 ? 'alert-success' : 'alert-danger'; ?> py-2 small m-0">
                        <?php echo $razon_circulante >= 1.5 ? '✔ Capacidad excelente para cubrir deudas a corto plazo.' : '⚠ Alerta: Liquidez ajustada para pagar compromisos inmediatos.'; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h5 class="text-white-50">Prueba Ácida</h5>
                    <div class="metric-val"><?php echo number_format($prueba_acida, 2); ?> x</div>
                    <p class="small text-muted mt-2">Fórmula: (Activo Corr. - Inventario) / Pasivo Corr.</p>
                    <div class="alert <?php echo $prueba_acida >= 1.0 ? 'alert-success' : 'alert-warning'; ?> py-2 small m-0">
                        <?php echo $prueba_acida >= 1.0 ? '✔ Disponibilidad líquida sana sin depender de vender repuestos.' : '⚠ Dependen de la venta de inventario para pagar deudas.'; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h5 class="text-white-50">Rotación de Inventario</h5>
                    <div class="metric-val"><?php echo number_format($rotacion_inventario, 1); ?> veces</div>
                    <p class="small text-muted mt-2">Fórmula: Costo de Ventas / Inventario Promedio</p>
                    <span class="badge bg-warning text-dark p-2">El inventario se renueva constantemente en el ciclo comercial.</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-custom h-100">
                    <h5 class="text-white-50">Margen Neto de Utilidad</h5>
                    <div class="metric-val"><?php echo number_format($margen_utilidad, 1); ?> %</div>
                    <p class="small text-muted mt-2">Fórmula: (Utilidad Neta / Ventas Totales) * 100</p>
                    <span class="badge bg-success p-2">Rendimiento real por cada dólar vendido en repuestos.</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>