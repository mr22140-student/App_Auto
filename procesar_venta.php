<?php
include("conexion.php");

// =========================================================================
// FUNCIONES DE SOPORTE (Declaradas al principio para evitar errores)
// =========================================================================
function obtenerIdCuenta($conexion, $codigo_cuenta) {
    $codigo_cuenta = mysqli_real_escape_string($conexion, $codigo_cuenta);
    $resultado = mysqli_query($conexion, "SELECT id FROM catalogo_cuentas WHERE codigo = '$codigo_cuenta'");
    if ($row = mysqli_fetch_assoc($resultado)) {
        return intval($row['id']);
    }
    return null; 
}

// =========================================================================
// PROCESAMIENTO DE LA FORMULARIO DE VENTA
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cantidad'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $tipo_pago_form = $_POST['tipo_pago'];
    $cantidades = $_POST['cantidad'];
    $fecha_actual = date('Y-m-d H:i:s');

    $total_venta = 0;
    $productos_a_vender = [];

    // Calcular el monto total de la venta buscando precios reales
    foreach ($cantidades as $id_producto => $cantidad) {
        $cantidad = intval($cantidad);
        if ($cantidad > 0) {
            $res = mysqli_query($conn, "SELECT precio, stock FROM producto WHERE id = $id_producto");
            $prod = mysqli_fetch_assoc($res);
            
            if ($prod && $prod['stock'] >= $cantidad) {
                $subtotal = $prod['precio'] * $cantidad;
                $total_venta += $subtotal;
                $productos_a_vender[] = [
                    'id' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio' => $prod['precio']
                ];
            }
        }
    }

    if ($total_venta > 0) {
        // 1. Guardar el registro general en la tabla 'venta'
        $query_venta = "INSERT INTO venta (cliente_id, fecha, total) VALUES ($cliente_id, '$fecha_actual', $total_venta)";
        
        if (mysqli_query($conn, $query_venta)) {
            $venta_id = mysqli_insert_id($conn); // Recuperamos el ID de la venta creada

            // 2. Descontar las existencias del inventario y guardar el detalle de la venta
            foreach ($productos_a_vender as $p) {
                $pid = $p['id'];
                $cant = $p['cantidad'];
                $prc = $p['precio'];

                // Restar del stock
                mysqli_query($conn, "UPDATE producto SET stock = stock - $cant WHERE id = $pid");
                
                // Mapeo correcto de cantidad y precio_unitario
                mysqli_query($conn, "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) VALUES ($venta_id, $pid, $cant, $prc)");
            }

            // 3. Formatear las variables para tu tabla relacional 'pago'
            $subtipo_tarjeta = 'NINGUNO';
            if ($tipo_pago_form == 'TARJETA_DEBITO' || $tipo_pago_form == 'TARJETA_CREDITO') {
                $tipo_pago_base = 'TARJETA';
                $subtipo_tarjeta = ($tipo_pago_form == 'TARJETA_DEBITO') ? 'DEBITO' : 'CREDITO';
                $cuenta_destino_id = obtenerIdCuenta($conn, '1102'); // Código de BANCOS
            } else {
                $tipo_pago_base = 'EFECTIVO';
                $subtipo_tarjeta = 'NINGUNO';
                $cuenta_destino_id = obtenerIdCuenta($conn, '1101'); // Código de CAJA GENERAL
            }

            // Insertar en tu tabla 'pago'
            $query_pago = "INSERT INTO pago (tipo, subtipo_tarjeta, monto, compra_id) 
                           VALUES ('$tipo_pago_base', '$subtipo_tarjeta', $total_venta, NULL)";
            mysqli_query($conn, $query_pago);


            // =========================================================================
            // 4. AUTOMATIZACIÓN INTELIGENTE DEL LIBRO DIARIO
            // =========================================================================
            $cuenta_ventas_id      = obtenerIdCuenta($conn, '4101'); // Código de VENTAS
            $cuenta_inventario_id  = obtenerIdCuenta($conn, '1103'); // Código de INVENTARIO
            $cuenta_costo_venta_id = obtenerIdCuenta($conn, '5101'); // Código de COSTO DE VENTAS

            $descripcion_asiento = "Venta de repuestos bajo factura general - Venta #" . $venta_id;

            if ($cuenta_destino_id && $cuenta_ventas_id) {
                // --- REGISTRO 1: Entrada de efectivo o banco (Debe) ---
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_destino_id, '$fecha_actual', '$descripcion_asiento', $total_venta, 0)");

                // --- REGISTRO 2: Reconocimiento del ingreso (Haber) ---
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_ventas_id, '$fecha_actual', '$descripcion_asiento', 0, $total_venta)");
            }

            if ($cuenta_costo_venta_id && $cuenta_inventario_id) {
                // --- REGISTRO 3 y 4: Costo de Ventas e Inventario (60% estimado) ---
                $costo_estimado = $total_venta * 0.60;
                
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_costo_venta_id, '$fecha_actual', 'Reconocimiento del costo de repuestos vendidos', $costo_estimado, 0)");
                
                mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, fecha, descripcion, debe, haber) 
                                     VALUES ($cuenta_inventario_id, '$fecha_actual', 'Salida de existencias de almacén por venta', 0, $costo_estimado)");
            }
            // =========================================================================

            // Redireccionar al éxito
            header("Location: ventas.php?success=1");
            exit;
        } else {
            echo "Error crítico al guardar la venta: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Debes seleccionar al menos un repuesto con cantidad válida.";
    }
}
?>