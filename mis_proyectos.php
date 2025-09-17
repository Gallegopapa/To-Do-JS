<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Consulta para obtener proyectos creados por el usuario
$sql = "SELECT p.id, p.name, p.description, p.is_archived, p.created_at, 
               u.name AS assigned_username
        FROM projects p
        LEFT JOIN users u ON p.assigned_to = u.id
        WHERE p.owner_id = $user_id
        ORDER BY p.created_at DESC";

$resultado = $conn->query($sql);

if ($resultado === false) {
    die("Error en la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="css/mis_proyectos.css"/>
  <title>Mis Proyectos</title>
</head>
<body>
  <div class="container">
    <h2>Mis Proyectos</h2>

    <?php if ($resultado->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Asignado a</th>
            <th>Creado el</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($proyecto = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($proyecto['name']) ?></td>
              <td><?= htmlspecialchars($proyecto['description']) ?></td>
              <td class="<?= $proyecto['is_archived'] ? 'estado-inactivo' : 'estado-activo' ?>">
                <?= $proyecto['is_archived'] ? 'Inactivo' : 'Activo' ?>
              </td>
              <td><?= $proyecto['assigned_username'] ? htmlspecialchars($proyecto['assigned_username']) : 'Sin asignar' ?></td>
              <td><?= htmlspecialchars($proyecto['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No tienes proyectos creados aún.</p>
    <?php endif; ?>

    <div class="volver">
      <a href="crear_proyecto.php">Crear Nuevo Proyecto</a>
      <a href="inicio.php" class="btn-volver">Volver a Inicio</a>
    </div>
  </div>
</body>
</html>
