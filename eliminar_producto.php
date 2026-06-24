<?php

include("conexion.php");

$id = $_GET['id'];

$sql = "DELETE FROM producto WHERE id=$id";

mysqli_query($conn,$sql);

header("Location:productos.php");

?>