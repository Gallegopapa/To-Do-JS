<?php
session_start();
include("config.php");

// Verifica sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// 🔹 Verificar que el usuario tenga acceso al proyecto
$check_sql = "SELECT id FROM projects 
              WHERE id = ? 
              AND (owner_id = ? OR assigned_to = ?)";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("iii", $project_id, $user_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("❌ No tienes acceso a este proyecto.");
}

// 🔹 Obtener las tareas del proyecto
$sql = "SELECT * FROM admin_tasks 
        WHERE project_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Mis Tareas</title>
  <link rel="stylesheet" href="css/ver_tapro.css" />
</head>
<body>
  <main class="contenido">
    <h2>📌 Tareas del Proyecto #<?= htmlspecialchars($project_id) ?></h2>

    <table>
      <tr>
        <th>Título</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Prioridad</th>
        <th>Fecha inicio</th>
        <th>Fecha vencimiento</th>
        <th>Archivo</th>
      </tr>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['description_md'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['prioridad'] ?? $row['priority'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['start_date'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['due_date'] ?? '') ?></td>
          <td>
            <?php if(!empty($row['archivo'])): ?>
              <a href="uploads/<?= urlencode($row['archivo']) ?>" target="_blank">Ver archivo</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">No hay tareas en este proyecto.</td></tr>
      <?php endif; ?>
    </table>

    <div style="margin-top:20px;">
      <button onclick="window.location.href='mis_proyectos.php'">Volver</button>
    </div>
  </main>
</body>
</html>
