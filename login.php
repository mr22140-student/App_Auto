<?php
session_start();
include("conexion.php");

if(isset($_POST['login'])){

    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuario
            WHERE correo='$correo'
            AND password='$password'";

    $resultado = mysqli_query($conn,$sql);

    if(mysqli_num_rows($resultado)>0){

        $usuario = mysqli_fetch_assoc($resultado);

        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header("Location:index.php");

    }else{
        echo "Datos incorrectos";
    }
}
?>