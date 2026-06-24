<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $correo   = $_POST['correo'];

    // Insertar en la nueva tabla cliente
    $sql = "INSERT INTO cliente (nombre, apellido, telefono, correo) 
            VALUES ('$nombre', '$apellido', '$telefono', '$correo')";

    mysqli_query($conn, $sql);

    // Redireccionar de vuelta a la lista de clientes
    header("Location: clientes.php");
    exit();
}
?>