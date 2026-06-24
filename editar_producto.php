<?php

include("conexion.php");

$id = $_GET['id'];

$sql = "SELECT * FROM producto WHERE id=$id";
$resultado = mysqli_query($conn,$sql);

$producto = mysqli_fetch_assoc($resultado);

?>