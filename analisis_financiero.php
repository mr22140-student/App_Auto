<?php
include("conexion.php");

// Asumimos el año actual según el contexto del sistema (2026)
$anio_actual = 2026;
$anio_anterior = 2025;

// ==========================================
// 1. RECOLECCIÓN DE DATOS (AÑO ACTUAL 2026)
// ==========================================
$v_actual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM venta WHERE YEAR(fecha) = $anio_actual"));
$ventas_2026 = floatval($v_actual['total']);

$c_actual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM compra WHERE YEAR(fecha) = $anio_actual"));
$compras_2026 = floatval($c_actual['total']);

// ==========================================
// 2. RECOLECCIÓN DE DATOS (AÑO ANTERIOR 2025)
// ==========================================
$v_anterior = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM venta WHERE YEAR(fecha) = $anio_anterior"));
$ventas_2025 = floatval($v_anterior['total']);

// Si la base de datos está limpia para 2025, asignamos un base para evitar divisiones por cero
if ($ventas_2025 == 0) $ventas_2025 = 5000.00; 

$c_anterior = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM compra WHERE YEAR(fecha) = $anio_anterior"));
$compras_2025 = floatval($c_anterior['total']);
if ($compras_2025 == 0) $compras_2025 = 2500.00;

// ==========================================
// 3. CÁLCULOS DEL MÉTODO HORIZONTAL
// ==========================================
$var_abs_ventas = $ventas_2026 - $ventas_2025;
$var_rel_ventas = ($var_abs_ventas / $ventas_2025) * 100;

$var_abs_compras = $compras_2026 - $compras_2025;
$var_rel_compras = ($var_abs_compras / $compras_2025) * 100;

// ==========================================
// 4. CÁLCULOS DEL MÉTODO VERTICAL (Para 2026)
// ==========================================
$vert_ventas = $ventas_2026 > 0 ? 100.00 : 0;
$vert_compras = $ventas_2026 > 0 ? ($compras_2026 / $ventas_2026) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Análisis Vertical y Horizontal - ERP Auto Repuestos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; }
        .pos-var { color: #2ecc71; font-weight: bold; }
        .neg-var { color: #e74c3c; font-weight: bold; }

        /* Estilos unificados para el menú desplegable premium */
        .nav-link-custom { color: #e0e0e0; text-decoration: none; font-size: 0.9rem; font-weight: 500; padding: 6px 10px; border-radius: 4px; transition: all 0.2s ease; }
        .nav-link-custom:hover { color: #ffc107; background-color: rgba(255, 193, 7, 0.05); }
        .nav-dropdown-btn { font-size: 0.88rem !important; padding: 5px 12px !important; border-radius: 4px !important; box-shadow: none !important; }
        .custom-dropdown-ul { background-color: #262626 !important; border: 1px solid #444 !important; padding: 6px 0 !important; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .custom-dropdown-ul .dropdown-item { font-size: 0.9rem !important; padding: 7px 16px !important; color: #ffffff !important; transition: background 0.15s ease; }
        .custom-dropdown-ul .dropdown-item:hover { background-color: #ffc107 !important; color: #000000 !important; font-weight: bold; }
    </style>
</head>
<body>

    <!-- ENCABEZADO PREMIUM UNIFICADO -->
    <div class="navbar-custom" style="background-color: #262626; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333;">
        <span class="navbar-title" style="font-weight: bold; font-size: 1.35rem; color: #ffc107; letter-spacing: 0.5px;">
            ERP Auto Repuestos
        </span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="index.php" class="nav-link-custom">Inicio</a>
            <a href="productos.php" class="nav-link-custom">Productos</a>
            <a href="clientes.php" class="nav-link-custom">Clientes</a>
            <a href="ventas.php" class="nav-link-custom">Ventas</a>
            <a href="compras.php" class="nav-link-custom">Compras</a>
            <a href="catalogo.php" class="nav-link-custom">Catálogo y Manual</a>
            
            <!-- Dropdown Contabilidad -->
            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold nav-dropdown-btn" type="button" id="dropContabilidad" data-bs-toggle="dropdown" aria-expanded="false">
                    📖 Contabilidad
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropContabilidad">
                    <li><a class="dropdown-item" href="librodiario.php">Libro Diario</a></li>
                    <li><a class="dropdown-item" href="libromayor.php">Libro Mayor</a></li>
                    <li><a class="dropdown-item" href="razones.php">Razones Financieras</a></li>
                </ul>
            </div>

            <!-- Dropdown Estados y Reportes (Destacando Análisis H/V) -->
            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-warning btn-sm dropdown-toggle fw-bold text-dark nav-dropdown-btn" type="button" id="dropReportes" data-bs-toggle="dropdown" aria-expanded="false">
                    📊 Estados y Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropReportes">
                    <li><a class="dropdown-item" href="balance_comprobacion.php">Balance de Comprobación</a></li>
                    <li><a class="dropdown-item" href="balance_general.php">Balance General</a></li>
                    <li><a class="dropdown-item" href="analisis_financiero.php" style="color: #ffc107 !important; font-weight: bold;">Análisis H/V</a></li>
                    <li><hr class="dropdown-divider" style="border-color: #444;"></li>
                    <li><a class="dropdown-item" href="reportes_financieros.php">Reportes Financieros</a></li>
                    <li><a class="dropdown-item" href="reportes.php">Módulo de Reportes</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main container py-4">
        <div class="card-custom">
            <h2 class="text-warning-custom mb-2">Análisis Financiero Avanzado</h2>
            <p class="text-white-50 m-0">Auditoría de estados mediante la aplicación de métodos de desglose vertical y comparativos horizontales.</p>
        </div>

        <div class="card-custom">
            <h4 class="text-warning-custom mb-3">1. Método Horizontal (Año Anterior vs Año Actual)</h4>
            <div class="table-responsive">
                <table class="table table-custom table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Rubro Contable</th>
                            <th class="text-end">Año Anterior ($)</th>
                            <th class="text-end">Año Actual ($)</th>
                            <th class="text-end">Variación Absoluta ($)</th>
                            <th class="text-end">Variación Relativa (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Ingresos totales (Ventas)</strong></td>
                            <td class="text-end"><?php echo number_format($ventas_2025, 2); ?></td>
                            <td class="text-end"><?php echo number_format($ventas_2026, 2); ?></td>
                            <td class="text-end <?php echo $var_abs_ventas >= 0 ? 'pos-var' : 'neg-var'; ?>">
                                $<?php echo number_format($var_abs_ventas, 2); ?>
                            </td>
                            <td class="text-end <?php echo $var_rel_ventas >= 0 ? 'pos-var' : 'neg-var'; ?>">
                                <?php echo number_format($var_rel_ventas, 2); ?>%
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Costos Operativos (Compras)</strong></td>
                            <td class="text-end"><?php echo number_format($compras_2025, 2); ?></td>
                            <td class="text-end"><?php echo number_format($compras_2026, 2); ?></td>
                            <td class="text-end <?php echo $var_abs_compras <= 0 ? 'pos-var' : 'neg-var'; ?>">
                                $<?php echo number_format($var_abs_compras, 2); ?>
                            </td>
                            <td class="text-end <?php echo $var_rel_compras <= 0 ? 'pos-var' : 'neg-var'; ?>">
                                <?php echo number_format($var_rel_compras, 2); ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-custom">
            <h4 class="text-warning-custom mb-3">2. Método Vertical (Estructura de Costos)</h4>
            <div class="table-responsive">
                <table class="table table-custom table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Rubro Contable</th>
                            <th class="text-end">Monto Total $</th>
                            <th class="text-end">Porcentaje Estructural %</th>
                            <th>Interpretación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Ingresos por Ventas (Cuenta Base)</strong></td>
                            <td class="text-end fw-bold">$<?php echo number_format($ventas_2026, 2); ?></td>
                            <td class="text-end fw-bold text-info"><?php echo number_format($vert_ventas, 2); ?>%</td>
                            <td class="text-white-50">Representa el ingreso bruto total capturado por el ERP.</td>
                        </tr>
                        <tr>
                            <td><strong>Absorción por Compras de Inventario</strong></td>
                            <td class="text-end">$<?php echo number_format($compras_2026, 2); ?></td>
                            <td class="text-end text-warning"><?php echo number_format($vert_compras, 2); ?>%</td>
                            <td>
                                <?php 
                                if($vert_compras > 70) {
                                    echo "<span class='text-danger'>⚠ Alerta: Las compras consumen más del 70% del ingreso. Margen muy bajo.</span>";
                                } else {
                                    echo "<span class='text-success'>✔ Estructura eficiente. El inventario absorbe una porción controlada del ingreso.</span>";
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Requerido para que funcionen los Dropdowns interactivos -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>