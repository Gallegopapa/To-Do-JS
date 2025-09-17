<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";

//esto obtiene usuarios para el select
$usuarios_resultado = $conn->query("SELECT id, name FROM users WHERE id != " . $_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $descripcion = $conn->real_escape_string($_POST["descripcion"]);
    $estado = isset($_POST["estado"]) && $_POST["estado"] === "inactivo" ? 1 : 0;
    $owner_id = $_SESSION["user_id"];
    $asignado_a = isset($_POST["asignar"]) && is_numeric($_POST["asignar"]) ? intval($_POST["asignar"]) : "NULL";

    $sql = "INSERT INTO projects (name, description, owner_id, is_archived, assigned_to)
            VALUES ('$nombre', '$descripcion', $owner_id, $estado, $asignado_a)";

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
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="stylesheet" href="css/crear_proyecto.css"/>
  <title>Crear Proyecto</title>
</head>
<body>
  <div class="container">
    <h2>Crear Nuevo Proyecto</h2>

    <?php if (!empty($mensaje)): ?>
      <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
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
        <option value="">Selecciona un usuario</option>
        <?php if ($usuarios_resultado && $usuarios_resultado->num_rows > 0): ?>
          <?php while ($usuario = $usuarios_resultado->fetch_assoc()): ?>
            <option value="<?php echo $usuario['id']; ?>">
              <?php echo htmlspecialchars($usuario['name']); ?>
            </option>
          <?php endwhile; ?>
        <?php else: ?>
          <option value="">No hay usuarios disponibles</option>
        <?php endif; ?>
      </select>

      <button type="submit">Crear Proyecto</button>
    </form>

    <div class="volver">
      <a href="mis_proyectos.php">Ver mis proyectos</a>
      <a href="inicio.php" class="btn-volver">Volver a Inicio</a>
    </div>
  </div>
</body>
</html>
