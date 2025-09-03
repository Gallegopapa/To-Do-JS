<?php
session_start();

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION["user_name"];
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="./css/inicio.css" />
    <title>Gestor De Tareas</title>
  </head>
  <body>
    <header class="header">
      <div class="container">
        <div class="btn-menu">
          <label for="btn-menu">☰</label>
        </div>
        <div class="logo">
          <h1>GESTOR TAREAS</h1>
        </div>
        <nav class="menu">
          <a href="tareas.php">Ver Tareas</a>
          <a href="#">Gestionar Tareas</a>
          <a href="logout.php">Cerrar sesión</a>
        </nav>
      </div>
    </header>
    <div class="capa"></div>
    <input type="checkbox" id="btn-menu" />
    <div class="container-menu">
        <div class="cont-menu">
            <nav>
            <h1 class="bienvenida">Bienvenido a tu gestor de tareas, <?= htmlspecialchars($user_name) ?> </h1>
            </nav>
            <label for="btn-menu">✘</label>
        </div>
    </div>
  </body>

</html>
