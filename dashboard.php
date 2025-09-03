<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
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
