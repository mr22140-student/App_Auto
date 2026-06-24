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
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        .card-custom {
            background: #262626;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        .text-secondary-custom {
            color: #ffc107 !important;
        }
        .table-custom {
            color: #ffffff;
            background-color: #262626;
        }
        .form-control-dark {
            background-color: #333;
            border: 1px solid #444;
            color: #fff;
        }
        .form-control-dark:focus {
            background-color: #3a3a3a;
            color: #fff;
            border-color: #ffc107;
            box-shadow: none;
        }
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
            <h2 class="mb-4 text-secondary-custom">Registrar Nueva Venta</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success bg-success text-white border-0">¡Venta y lote de pago procesados con éxito!</div>
            <?php endif; ?>

            <form action="procesar_venta.php" method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Seleccionar Cliente</label>
                        <select name="cliente_id" class="form-select form-control-dark" required>
                            <?php while($c = mysqli_fetch_assoc($clientes)){ ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre']." ".$c['apellido']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Método de Pago</label>
                        <select name="tipo_pago" class="form-select form-control-dark" required>
                            <option value="EFECTIVO">Efectivo (Caja)</option>
                            <option value="TARJETA_DEBITO">Tarjeta de Débito</option>
                            <option value="TARJETA_CREDITO">Tarjeta de Crédito</option>
                        </select>
                    </div>
                </div>

                <h4 class="mb-3 text-white-50">Inventario de Repuestos Disponible</h4>
                <div class="table-responsive">
                    <table class="table table-custom table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Repuesto / Marca</th>
                                <th class="text-center">Precio Unitario</th>
                                <th class="text-center">Existencias</th>
                                <th style="width: 180px;" class="text-center">Cantidad a Vender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($productos)){ ?>
                            <tr>
                                <td><strong><?php echo $p['nombre']; ?></strong> <br><small class="text-white-50">Marca: <?php echo $p['marca']; ?> | Cód: <?php echo $p['codigo']; ?></small></td>
                                <td class="text-center fw-bold text-warning">$<?php echo number_format($p['precio'], 2); ?></td>
                                <td class="text-center"><span class="badge bg-secondary fs-6"><?php echo $p['stock']; ?> uds</span></td>
                                <td>
                                    <input type="number" name="cantidad[<?php echo $p['id']; ?>]" class="form-control form-control-dark text-center" min="0" max="<?php echo $p['stock']; ?>" value="0">
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-warning btn-lg w-100 mt-4 fw-bold text-dark">Procesar Transacción Comercial</button>
            </form>
        </div>
    </div>
</body>
</html>