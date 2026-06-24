<?php
include("conexion.php");

if(isset($_POST['registro'])){

    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "
    INSERT INTO usuario(nombre,correo,password,tipo)
    VALUES('$nombre','$correo','$password','cliente')
    ";

    mysqli_query($conn,$sql);

    header("Location:login.php");
}
?>