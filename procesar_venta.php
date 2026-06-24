<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $tipo_pago = $_POST['tipo_pago']; // Capturado desde el nuevo select de ventas.php
    $cantidades = $_POST['cantidad'] ?? [];
    
    $total = 0;
    $detalles_venta = [];

    // 1. Calcular Totales y validar existencias
    foreach ($cantidades as $producto_id => $cantidad) {
        $cantidad = intval($cantidad);
        if ($cantidad > 0) {
            $producto_id = intval($producto_id);
            $res = mysqli_query($conn, "SELECT precio, stock, nombre FROM producto WHERE id = $producto_id");
            $producto = mysqli_fetch_assoc($res);
            
            if ($producto['stock'] < $cantidad) {
                die("<script>alert('Stock insuficiente para: " . $producto['nombre'] . "'); window.history.back();</script>");
            }
            
            $subtotal = $producto['precio'] * $cantidad;
            $total += $subtotal;
            $detalles_venta[] = [
                'id' => $producto_id,
                'cantidad' => $cantidad,
                'precio' => $producto['precio']
            ];
        }
    }

    if ($total == 0) {
        die("<script>alert('Debe seleccionar al menos un producto.'); window.history.back();</script>");
    }

    // 2. Insertar Venta
    mysqli_query($conn, "INSERT INTO venta (cliente_id, tipo_pago, total) VALUES ($cliente_id, '$tipo_pago', $total)");
    $venta_id = mysqli_insert_id($conn);

    // 3. Procesar detalles y descontar Stock
    foreach ($detalles_venta as $item) {
        mysqli_query($conn, "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) 
                             VALUES ($venta_id, {$item['id']}, {$item['cantidad']}, {$item['precio']})");
        mysqli_query($conn, "UPDATE producto SET stock = stock - {$item['cantidad']} WHERE id = {$item['id']}");
    }

    // 4. CONTABILIZACIÓN AUTOMÁTICA EN PARTIDAS
    mysqli_query($conn, "INSERT INTO partida (descripcion) VALUES ('Venta de repuestos Ref Venta #$venta_id')");
    $partida_id = mysqli_insert_id($conn);

    $iva = $total * 0.13;
    $neto_venta = $total - $iva;

    // Determinar cuenta de cargo (DEBE) según método de pago
    // 101 = Caja General, 102 = Banco
    $cuenta_ingreso = ($tipo_pago === 'EFECTIVO') ? 101 : 102; 

    // Guardar detalles de la partida
    mysqli_query($conn, "INSERT INTO detalle_partida (partida_id, cuenta_id, debe, haber) VALUES ($partida_id, $cuenta_ingreso, $total, 0)");
    mysqli_query($conn, "INSERT INTO detalle_partida (partida_id, cuenta_id, debe, haber) VALUES ($partida_id, 401, 0, $neto_venta)");
    mysqli_query($conn, "INSERT INTO detalle_partida (partida_id, cuenta_id, debe, haber) VALUES ($partida_id, 202, 0, $iva)");


    // 5. REGISTROS EN LIBRO DIARIO (Corregido con cuenta_id obligatorio)
    // Asiento DEBE: Ingreso de Dinero (Caja o Banco)
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES ($cuenta_ingreso, 'Ingreso por Venta Ref #$venta_id', $total, 0)");

    // Asiento HABER: Ventas Netas (401)
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES (401, 'Venta de Repuestos Netas - Ref #$venta_id', 0, $neto_venta)");

    // Asiento HABER: IVA Débito Fiscal (202)
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES (202, 'IVA Débito Fiscal 13% - Ref #$venta_id', 0, $iva)");

    header("Location: ventas.php?success=1");
    exit;
}
?>