<?php
session_start();
include("config.php");

// Redirige si no hay sesión iniciada
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Escapar datos para evitar SQL Injection
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $descripcion = $conn->real_escape_string($_POST["descripcion"]);
    // Determinar estado: 0 = activo, 1 = archivado/inactivo
    $is_archived = isset($_POST["estado"]) && $_POST["estado"] === "inactivo" ? 1 : 0;
    $owner_id = $_SESSION["user_id"];

    $sql = "INSERT INTO projects (name, description, owner_id, is_archived) 
            VALUES ('$nombre', '$descripcion', $owner_id, $is_archived)";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Proyecto creado correctamente.";
    } else {
        $mensaje = "Error al crear proyecto: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Crear Proyecto</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f1f1f1;
    margin: 0;
    padding: 0;
  }
  .container {
    max-width: 600px;
    background: white;
    padding: 30px;
    margin: 50px auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
  }
  h2 {
    text-align: center;
    color: #333;
  }
  form label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }
  form input[type="text"],
  form textarea,
  form select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
  }
  form button {
    margin-top: 20px;
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
  }
  form button:hover {
    background-color: #45a049;
  }
  .mensaje {
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
    color: green;
  }
  .volver {
    text-align: center;
    margin-top: 20px;
  }
  .volver a {
    display: inline-block;
    background-color: #3b82f6;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }
  .volver a:hover {
    background-color: #2563eb;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Crear Nuevo Proyecto</h2>

    <?php if ($mensaje): ?>
      <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="nombre">Nombre del Proyecto:</label>
      <input type="text" name="nombre" id="nombre" required />

      <label for="descripcion">Descripción:</label>
      <textarea name="descripcion" id="descripcion" rows="4" required></textarea>

      <label for="estado">Estado:</label>
      <select name="estado" id="estado" required>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>

      <label for="asignar">Asignar a:</label>
      <select name="asignar" id="asignar" required>
        <option value=""></option>
        <option value=""></option>
      </select>

      <button type="submit">Crear Proyecto</button>
    </form>

    <div class="volver">
      <a href="inicio.php">  Volver a Inicio</a>
    </div>
  </div>
</body>
</html>
