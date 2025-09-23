<?php
require "config.php";
require "auth.php";

requiereLogin(); //esto asegura de que este logueado

//solo admin puede entrar "rol_id = 1"
if ($_SESSION['usuario']['rol_id'] != 1) {
    die("Acceso denegado: Solo administradores");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
</head>
<body>
  <h2>Bienvenido <?= htmlspecialchars($_SESSION["user_name"]) ?> 🎉</h2>
  <p>Estás autenticado correctamente.</p>
  <a href="logout.php">Cerrar sesión</a>
</body>
</html>
