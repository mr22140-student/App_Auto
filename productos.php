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
    <title>Productos - ERP Auto Repuestos</title>
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
        
        /* Estilos integrados para evitar que rompa el diseño del menú */
        .nav-link-custom { color: #e0e0e0; text-decoration: none; font-size: 0.9rem; font-weight: 500; padding: 6px 10px; border-radius: 4px; transition: all 0.2s ease; }
        .nav-link-custom:hover { color: #ffc107; background-color: rgba(255, 193, 7, 0.05); }
        .nav-dropdown-btn { font-size: 0.88rem !important; padding: 5px 12px !important; border-radius: 4px !important; box-shadow: none !important; }
        .custom-dropdown-ul { background-color: #262626 !important; border: 1px solid #444 !important; padding: 6px 0 !important; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .custom-dropdown-ul .dropdown-item { font-size: 0.9rem !important; padding: 7px 16px !important; color: #ffffff !important; transition: background 0.15s ease; }
        .custom-dropdown-ul .dropdown-item:hover { background-color: #ffc107 !important; color: #000000 !important; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar-custom" style="background-color: #262626; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333;">
        <span class="navbar-title" style="font-weight: bold; font-size: 1.35rem; color: #ffc107; letter-spacing: 0.5px;">
            ERP Auto Repuestos
        </span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="index.php" class="nav-link-custom">Inicio</a>
            <a href="productos.php" class="nav-link-custom" style="color: #ffc107; font-weight: bold;">Productos</a>
            <a href="clientes.php" class="nav-link-custom">Clientes</a>
            <a href="ventas.php" class="nav-link-custom">Ventas</a>
            <a href="compras.php" class="nav-link-custom">Compras</a>
            <a href="catalogo.php" class="nav-link-custom">Catálogo y Manual</a>
            
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

            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-warning btn-sm dropdown-toggle fw-bold text-dark nav-dropdown-btn" type="button" id="dropReportes" data-bs-toggle="dropdown" aria-expanded="false">
                    📊 Estados y Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropReportes">
                    <li><a class="dropdown-item" href="balance_comprobacion.php">Balance de Comprobación</a></li>
                    <li><a class="dropdown-item" href="balance_general.php">Balance General</a></li>
                    <li><a class="dropdown-item" href="analisis_financiero.php">Análisis H/V</a></li>
                    <li><hr class="dropdown-divider" style="border-color: #444;"></li>
                    <li><a class="dropdown-item" href="reportes_financieros.php">Reportes Financieros</a></li>
                    <li><a class="dropdown-item" href="reportes.php">Módulo de Reportes</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main container py-4">
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
                            <td><code><?php echo htmlspecialchars($f['codigo'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                            <td><strong><?php echo htmlspecialchars($f['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo htmlspecialchars($f['marca'], ENT_QUOTES, 'UTF-8'); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>