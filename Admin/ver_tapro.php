<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$is_admin = isset($_SESSION['is_admin']) ? (bool)$_SESSION['is_admin'] : false;
include("../config.php");

// Recibe el id del proyecto por GET
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

  $stmt = $conn->prepare("SELECT * FROM admin_tasks WHERE project_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $project_id);

// Preparar la consulta según caso
if ($project_id > 0) {
    if ($is_admin) {
        $stmt = $conn->prepare("SELECT * FROM admin_tasks WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $project_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_tasks WHERE creator_id = ? AND project_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("ii", $user_id, $project_id);
    }
} else {
    if ($is_admin) {
        $stmt = $conn->prepare("SELECT * FROM admin_tasks ORDER BY created_at DESC");
        // no bind necesario
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_tasks WHERE creator_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
    }
}

// Verificar que la preparación fue exitosa
if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Tareas Asignadas</title>
    <link rel="stylesheet" href="../css/ver_tapro.css" />
</head>
<body>
    <main class="contenido">
        <h2>
            <?php
            if ($project_id > 0) {
                echo "Tareas asignadas del Proyecto #".htmlspecialchars($project_id);
            } else {
                echo $is_admin ? "Todas las tareas asignadas" : "Mis tareas";
            }
            ?>
        </h2>
        <table>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Fecha inicio</th>
                <th>Fecha vencimiento</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['description_md'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['prioridad'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['start_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['due_date'] ?? '') ?></td>
                    <td>
                        <?php if(!empty($row['archivo'])): ?>
                            <a href="uploads/<?= urlencode($row['archivo']) ?>" target="_blank">Ver archivo</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="acciones">
                        <a href="editar_tarea.php?id=<?= htmlspecialchars($row['id']) ?>">
                            <img src="../svg/lucide--edit(1).svg" alt="Editar">
                        </a>
                        <a href="eliminar_tarea.php?id=<?= htmlspecialchars($row['id']) ?>"
                            onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')">
                            <img src="../svg/material-symbols--close (1).svg" alt="Eliminar">
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;">No hay tareas para mostrar.</td>
                </tr>
            <?php endif; ?>
        </table>
        <div style="margin-top:20px;">
            <button onclick="window.location.href='asignar_proyectos.php'">Crear nueva tarea</button>
            <button onclick="window.location.href='proyectos_Admin.php'">Volver</button>
        </div>
    </main>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
