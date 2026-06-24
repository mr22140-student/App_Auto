<form action="procesar_compra.php" method="POST">

    <input type="text" name="proveedor" placeholder="Proveedor" required>

    <!-- productos en arrays -->
    <input type="number" name="cantidad[1]" placeholder="Cantidad producto 1">
    <input type="number" name="precio[1]" placeholder="Precio producto 1">

    <input type="number" name="cantidad[2]" placeholder="Cantidad producto 2">
    <input type="number" name="precio[2]" placeholder="Precio producto 2">

    <select name="tipo_pago">
        <option value="EFECTIVO">Efectivo</option>
        <option value="TARJETA">Tarjeta</option>
    </select>

    <select name="subtipo_tarjeta">
        <option value="">N/A</option>
        <option value="CREDITO">Crédito</option>
        <option value="DEBITO">Débito</option>
    </select>

    <button type="submit">Procesar compra</button>

</form>