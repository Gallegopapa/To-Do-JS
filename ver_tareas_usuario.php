<?php
session_start();
include("config.php");

// Verifica que haya sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Capturar el proyecto actual (si se pasa por GET)
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// 🔹 Obtener los proyectos en los que participa el usuario
$projects_sql = "SELECT project_id FROM proyectos_usuarios WHERE user_id = ?";
$stmt = $conn->prepare($projects_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$project_ids = [];
while ($p = $res->fetch_assoc()) {
    $project_ids[] = $p['project_id'];
}

$result = null;
if (!empty($project_ids)) {
    $ids = implode(",", $project_ids);

    if ($project_id == 0) {
        // 🔹 Todas las tareas de todos los proyectos del usuario
        $sql = "SELECT * FROM admin_tasks 
                WHERE project_id IN ($ids) 
                ORDER BY created_at DESC";
        $result = $conn->query($sql);
    } else {
        // 🔹 Solo las tareas del proyecto seleccionado
        $sql = "SELECT * FROM admin_tasks 
                WHERE project_id = ? 
                AND project_id IN ($ids) 
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}
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
    <h2>📌 Mis tareas asignadas</h2>

    <?php if (empty($project_ids)): ?>
      <p>No estás asignado a ningún proyecto aún.</p>
    <?php else: ?>
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
          <tr><td colspan="7">No tienes tareas asignadas en este proyecto.</td></tr>
        <?php endif; ?>
      </table>
    <?php endif; ?>

    <div style="margin-top:20px;">
      <button onclick="window.location.href='mis_proyectos.php'">Volver</button>
    </div>
  </main>
</body>
</html>
            