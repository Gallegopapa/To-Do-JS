<?php
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password, $conexion)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Usuario o contraseña incorrectos <br>";
        echo "<a href='login.php'>Volver</a>";
    }
}
?>
