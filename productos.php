<?php
include("conexion.php");

// Procesar el guardado si viene el formulario anterior
if(isset($_POST['codigo']) && isset($_POST['guardar'])){
    $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if($precio > 0 && $stock >= 0) {
        mysqli_query($conn, "INSERT INTO producto (codigo, nombre, marca, precio, stock) VALUES ('$codigo', '$nombre', '$marca', $precio, $stock)");
        header("Location: productos.php");
        exit;
    }
}

$productos = mysqli_query($conn, "SELECT * FROM producto ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Ajustes de contraste para la plantilla de fondo oscuro */
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
            color: #ffc107 !important; /* Amarillo/Dorado para alta visibilidad */
        }
        .table-custom {
            color: #ffffff;
            background-color: #262626;
        }
        .input-group-text {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
            border: none;
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
    <div class="main">
        <!-- Formulario de Entrada Eficiente -->
        <div class="card-custom">
            <h3 class="mb-4 text-secondary-custom">Ingresar Nuevo Repuesto Catálogo</h3>
            <form action="productos.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label text-white-50">Código</label>
                        <input type="text" name="codigo" class="form-control" placeholder="Ej: REP-01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Descripción del Repuesto</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Pastillas de Freno" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50">Marca</label>
                        <input type="text" name="marca" class="form-control" placeholder="Ej: Bosch" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Precio Público</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <!-- Entrada fluida tipo texto, bloquea negativos y caracteres no numéricos al instante -->
                            <input 
                                type="text" 
                                name="precio" 
                                class="form-control text-end fs-5" 
                                placeholder="0.00" 
                                required
                                inputmode="decimal"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                            >
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50">Stock Inicial</label>
                        <input type="number" name="stock" min="0" class="form-control" placeholder="0" required>
                    </div>
                </div>
                <button type="submit" name="guardar" class="btn btn-warning btn-lg w-100 mt-4 fw-bold text-dark">Guardar en Catálogo</button>
            </form>
        </div>

        <!-- Tabla de Inventario con Contraste Mejorado -->
        <div class="card-custom">
            <h3 class="mb-3 text-white-50">Inventario Maestro</h3>
            <div class="table-responsive">
                <table class="table table-custom table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Marca</th>
                            <th class="text-end">Precio Venta</th>
                            <th class="text-center">Existencias</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($f = mysqli_fetch_assoc($productos)){ ?>
                        <tr>
                            <td><code><?php echo $f['codigo']; ?></code></td>
                            <td><strong><?php echo $f['nombre']; ?></strong></td>
                            <td><?php echo $f['marca']; ?></td>
                            <td class="text-end fw-bold text-warning">$<?php echo number_format($f['precio'], 2); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo ($f['stock'] > 5) ? 'bg-success' : 'bg-danger'; ?> fs-6">
                                    <?php echo $f['stock']; ?> uds
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="eliminar_producto.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que desea eliminar este repuesto?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>