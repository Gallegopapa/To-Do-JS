<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Consulta de proyectos creados por administradores
$sql = "SELECT p.id, p.name, p.description, p.is_archived, p.created_at, 
               u.name AS owner_username, 
               a.name AS assigned_username
        FROM projects p
        INNER JOIN users u ON p.owner_id = u.id
        LEFT JOIN users a ON p.assigned_to = a.id
        WHERE u.role = 'admin' AND p.is_archived = 0
        ORDER BY p.created_at DESC";

$resultado = $conn->query($sql);
if ($resultado === false) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Duplicamos la consulta para obtener un proyecto para el botón inferior
$resultado2 = $conn->query($sql);
if ($resultado2 === false) {
    die("Error en la consulta SQL: " . $conn->error);
}
$primer_proyecto = $resultado2->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="stylesheet" href="../css/css_proyectos.css"/>
  <title>Proyectos de Administradores</title>
</head>
<body>
  <div class="container">
    <h2>Proyectos Creados por Administradores</h2>

    <?php if ($resultado->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Asignado al usuario:</th>
            <th>Administrador Creador</th>
            <th>Creado el</th>
            <th>Acciones</th>
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
              <td><?= htmlspecialchars($proyecto['owner_username']) ?></td>
              <td><?= htmlspecialchars($proyecto['created_at']) ?></td>
                      <td class="acciones">
          <a href="editar_proyectos.php?id=<?= $proyecto['id'] ?>">
            <img src="../svg/lucide--edit(1).svg" alt="Editar">
          </a>
          <a href="eliminar_proyectos.php?id=<?= $proyecto['id'] ?>" 
            onclick="return confirm('¿Seguro que deseas eliminar este proyecto?')">
            <img src="../svg/material-symbols--close (1).svg" alt="Eliminar">
          </a>
        </td>

            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No hay proyectos creados por administradores aún.</p>
    <?php endif; ?>

    <div class="volver">
      <a href="inico_Admin.php" class="btn-volver">Volver a Inicio</a>

    </div>
  </div>
</body>
</html>
