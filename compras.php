<?php
include("conexion.php");
$productos = mysqli_query($conn, "SELECT * FROM producto");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Compra - ERP</title>
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
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main">
        <div class="card-custom">
            <h2 class="mb-4 text-secondary">Registrar Entrada de Inventario (Compra)</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">¡La compra e inventario han sido actualizados junto con su asiento contable!</div>
            <?php endif; ?>

            <form action="procesar_compra.php" method="POST">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nombre del Proveedor</label>
                        <input type="text" name="proveedor" class="form-control" placeholder="Ej: Repuestos Central S.A." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Condición de Pago</label>
                        <select name="tipo_pago" class="form-select" required>
                            <option value="EFECTIVO">Efectivo (Caja)</option>
                            <option value="CREDITO">Crédito (Cuentas por Pagar)</option>
                        </select>
                    </div>
                </div>

                <h4 class="mb-3 text-muted">Lista de Repuestos de la Empresa</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Repuesto</th>
                                <th>Stock Actual</th>
                                <th style="width: 160px;">Cantidad Entrante</th>
                                <th style="width: 160px;">Costo Unitario ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($productos)){ ?>
                            <tr>
                                <td><strong><?php echo $p['nombre']; ?></strong> <br><small class="text-muted">Cód: <?php echo $p['codigo']; ?></small></td>
                                <td><span class="badge bg-dark"><?php echo $p['stock']; ?> uds</span></td>
                                <td>
                                    <input type="number" name="cantidad[<?php echo $p['id']; ?>]" class="form-control" min="0" value="0">
                                </td>
                                <td>
                                    <input type="number" name="precio[<?php echo $p['id']; ?>]" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-custom w-100 mt-3">Registrar Entrada y Generar Partida</button>
            </form>
        </div>
    </div>
</body>
</html>