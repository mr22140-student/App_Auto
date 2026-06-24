<?php
include("conexion.php");
$clientes = mysqli_query($conn, "SELECT * FROM cliente");
$productos = mysqli_query($conn, "SELECT * FROM producto WHERE stock > 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta - ERP</title>
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
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom">
            <h2 class="mb-4 text-secondary">Registrar Nueva Venta</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">¡Venta y asiento contable procesados con éxito!</div>
            <?php endif; ?>

            <form action="procesar_venta.php" method="POST">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Seleccionar Cliente</label>
                        <select name="cliente_id" class="form-select" required>
                            <?php while($c = mysqli_fetch_assoc($clientes)){ ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre']." ".$c['apellido']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Método de Pago</label>
                        <select name="tipo_pago" class="form-select" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA_DEBITO">Tarjeta de Débito</option>
                            <option value="TARJETA_CREDITO">Tarjeta de Crédito</option>
                        </select>
                    </div>
                </div>

                <h4 class="mb-3 text-muted">Inventario Disponible</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th>Precio Unitario</th>
                                <th>Existencias</th>
                                <th style="width: 150px;">Cantidad a Vender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($productos)){ ?>
                            <tr>
                                <td><strong><?php echo $p['nombre']; ?></strong> <small class="text-muted">(<?php echo $p['marca']; ?>)</small></td>
                                <td>$<?php echo number_format($p['precio'], 2); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $p['stock']; ?> uds</span></td>
                                <td>
                                    <input type="number" name="cantidad[<?php echo $p['id']; ?>]" class="form-control" min="0" max="<?php echo $p['stock']; ?>" value="0">
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-custom w-100 mt-3">Procesar Transacción Comercial</button>
            </form>
        </div>
    </div>
</body>
</html>