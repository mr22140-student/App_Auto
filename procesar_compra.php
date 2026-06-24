<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proveedor = mysqli_real_escape_string($conn, $_POST['proveedor']);
    $tipo_pago = $_POST['tipo_pago']; // EFECTIVO o TARJETA (que mapea a Banco/Proveedores)
    $cantidades = $_POST['cantidad'] ?? [];
    $precios = $_POST['precio'] ?? [];

    $total = 0;
    $detalles_compra = [];

    // 1. Calcular el total acumulado
    foreach ($cantidades as $id => $cantidad) {
        $cantidad = intval($cantidad);
        if ($cantidad > 0 && isset($precios[$id])) {
            $id = intval($id);
            $precio_costo = floatval($precios[$id]);
            $subtotal = $cantidad * $precio_costo;
            $total += $subtotal;

            $detalles_compra[] = [
                'producto_id' => $id,
                'cantidad' => $cantidad,
                'precio' => $precio_costo
            ];
        }
    }

    if ($total == 0) {
        die("<script>alert('Debe ingresar cantidades y costos válidos.'); window.history.back();</script>");
    }

    // 2. Registrar la cabecera de la compra
    mysqli_query($conn, "INSERT INTO compra (proveedor, total) VALUES ('$proveedor', $total)");
    $compra_id = mysqli_insert_id($conn);

    // 3. Insertar detalles e incrementar el stock en el inventario
    foreach ($detalles_compra as $item) {
        mysqli_query($conn, "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio_unitario) 
                             VALUES ($compra_id, {$item['producto_id']}, {$item['cantidad']}, {$item['precio']})");
        
        mysqli_query($conn, "UPDATE producto SET stock = stock + {$item['cantidad']} WHERE id = {$item['producto_id']}");
    }

    // 4. ASENTO CONTABLE AUTOMÁTICO (Partida Doble para Compras)
    $iva_credito = $total * 0.13;
    $inventario_neto = $total - $iva_credito;

    // DEBE: Aumento de Inventario de Repuestos
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES (103, 'Ingreso al almacén - Compra Ref #$compra_id', $inventario_neto, 0)");

    // DEBE: IVA Crédito Fiscal
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES (104, 'IVA Crédito Fiscal por Compra', $iva_credito, 0)");

    // HABER: Salida de fondos o cuenta por pagar
    if ($tipo_pago === 'EFECTIVO') {
        $cuenta_salida = 101; // Caja General
        $glosa = "Pago en efectivo a proveedor: $proveedor";
    } else {
        $cuenta_salida = 201; // Cuentas por Pagar
        $glosa = "Adquisición al crédito / Proveedor: $proveedor";
    }
    
    mysqli_query($conn, "INSERT INTO libro_diario (cuenta_id, descripcion, debe, haber) VALUES ($cuenta_salida, '$glosa', 0, $total)");

    header("Location: compras.php?success=1");
    exit;
}
?>