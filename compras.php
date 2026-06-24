<?php
include("conexion.php");

// =========================================================================
// FUNCIONES DE SOPORTE (Búsqueda automatizada por código contable)
// =========================================================================
function obtenerIdCuenta($conexion, $codigo_cuenta) {
    $codigo_cuenta = mysqli_real_escape_string($conexion, $codigo_cuenta);
    $resultado = mysqli_query($conexion, "SELECT id FROM catalogo_cuentas WHERE codigo = '$codigo_cuenta'");
    if ($row = mysqli_fetch_assoc($resultado)) {
        return intval($row['id']);
    }
    return null; 
}

// Procesar el formulario de compra si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_compra'])) {
    $proveedor = mysqli_real_escape_string($conn, $_POST['proveedor']);
    $condicion_pago = mysqli_real_escape_string($conn, $_POST['condicion_pago']);
    $cantidades = $_POST['cantidad'];
    $costos = $_POST['costo'];
    $fecha_actual = date('Y-m-d H:i:s');

    $total_compra = 0;
    $lote_productos = [];

    // Validar y acumular los totales primero
    foreach ($cantidades as $producto_id => $cantidad) {
        $cantidad = intval($cantidad);
        $costo_unitario = floatval($costos[$producto_id]);

        if ($cantidad > 0 && $costo_unitario > 0) {
            $subtotal = $cantidad * $costo_unitario;
            $total_compra += $subtotal;
            $lote_productos[] = [
                'id' => $producto_id,
                'cantidad' => $cantidad,
                'costo' => $costo_unitario
            ];
        }
    }

    if ($total_compra > 0) {
        // 1. Insertar el registro principal en la tabla 'compra'
        // NOTA: Si tu tabla 'compra' tiene columnas específicas (ej: proveedor, total, fecha), adáptalas aquí:
        $query_compra = "INSERT INTO compra (proveedor, fecha, total) VALUES ('$proveedor', '$fecha_actual', $total_compra)";
        
        if (mysqli_query($conn, $query_compra)) {
            $compra_id = mysqli_insert_id($conn); // Recuperamos el ID de la compra

            // 2. Actualizar stock e insertar el desglose en 'detalle_compra'
            foreach ($lote_productos as $item) {
                $pid = $item['id'];
                $cant = $item['cantidad'];
                $cst = $item['costo'];

                // Aumentar existencias en inventario
                mysqli_query($conn, "UPDATE producto SET stock = stock + $cant WHERE id = $pid");

                // Insertar en detalle_compra
                mysqli_query($conn, "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio_unitario) VALUES ($compra_id, $pid, $cant, $cst)");
            }

            // 3. Registrar el método de pago en la tabla relacional 'pago'
            $tipo_pago_base = ($condicion_pago == 'CREDITO') ? 'CREDITO' : 'EFECTIVO';
            $query_pago = "INSERT INTO pago (tipo, subtipo_tarjeta, monto, compra_id) 
                           VALUES ('$tipo_pago_base', 'NINGUNO', $total_compra, $compra_id)";
            mysqli_query($conn, $query_pago);

            // =========================================================================
            // 4. AUTOMATIZACIÓN INTELIGENTE DEL LIBRO DIARIO (COMPRAS)
            // =========================================================================
            $cuenta_inventario_id = obtenerIdCuenta($conn, '1103'); // Código para Inventario de Repuestos

            if ($condicion_pago == 'CREDITO') {
                $cuenta_pago_id = obtenerIdCuenta($conn, '2101'); // Código para Cuentas por Pagar (Proveedores)
                $txt_pago = "a Crédito (Proveedores)";
            } else {
                $cuenta_pago_id = obtenerIdCuenta($conn, '1101'); // Código para Caja General (Efectivo)
                $txt_pago = "en Efectivo (Caja)";
            }

            $descripcion_asiento = "Adquisición de mercancía / Repuestos " . $txt_pago . " - Compra #" . $compra_id;

            if ($cuenta_inventario_id && $cuenta_pago_id) {
                // --- REGISTRO 1: Aumento del Activo en Inventario (Debe) ---
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_inventario_id, '$fecha_actual', '$descripcion_asiento', $total_compra, 0)");

                // --- REGISTRO 2: Salida de Caja o Aumento de Obligación (Haber) ---
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_pago_id, '$fecha_actual', '$descripcion_asiento', 0, $total_compra)");
            }
            // =========================================================================

            header("Location: compras.php?success=1");
            exit;
        } else {
            echo "Error al registrar la compra: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Debes ingresar cantidades y costos mayores a cero.";
    }
}

$productos = mysqli_query($conn, "SELECT * FROM producto ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Compra - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 30px; }
        .text-secondary-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; }
        .input-group-text { background-color: #ffc107; color: #000; font-weight: bold; border: none; }
        .form-control-dark { background-color: #333; border: 1px solid #444; color: #fff; }
        .form-control-dark:focus { background-color: #3a3a3a; color: #fff; border-color: #ffc107; box-shadow: none; }
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
        <div class="card-custom">
            <h2 class="mb-4 text-secondary-custom">Registrar Entrada de Inventario (Compra)</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success bg-success text-white border-0">¡Entrada de inventario, lote de pago y asiento diario registrados correctamente!</div>
            <?php endif; ?>

            <form action="compras.php" method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Nombre del Proveedor</label>
                        <input type="text" name="proveedor" class="form-control form-control-dark" placeholder="Ej: Repuestos Central S.A." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Condición de Pago</label>
                        <select name="condicion_pago" class="form-select form-control-dark" required>
                            <option value="EFECTIVO">Efectivo (Caja)</option>
                            <option value="CREDITO">Crédito (Proveedores)</option>
                        </select>
                    </div>
                </div>

                <h4 class="mb-3 text-white-50">Lista de Repuestos de la Empresa</h4>
                <div class="table-responsive">
                    <table class="table table-custom table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Repuesto / Detalles</th>
                                <th class="text-center">Stock Actual</th>
                                <th style="width: 150px;" class="text-center">Cantidad Entrante</th>
                                <th style="width: 200px;" class="text-center">Costo Unitario ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($productos)){ ?>
                            <tr>
                                <td>
                                    <strong><?php echo $p['nombre']; ?></strong><br>
                                    <small class="text-white-50">Cód: <?php echo $p['codigo']; ?> | Marca: <?php echo $p['marca']; ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary fs-6"><?php echo $p['stock']; ?> uds</span>
                                </td>
                                <td>
                                    <input type="number" name="cantidad[<?php echo $p['id']; ?>]" class="form-control form-control-dark text-center" min="0" value="0">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input 
                                            type="text" 
                                            name="costo[<?php echo $p['id']; ?>]" 
                                            class="form-control form-control-dark text-end" 
                                            placeholder="0.00" 
                                            inputmode="decimal"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        >
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <button type="submit" name="registrar_compra" class="btn btn-warning btn-lg w-100 mt-4 fw-bold text-dark">Registrar Entrada y Generar Partida</button>
            </form>
        </div>
    </div>
</body>
</html>