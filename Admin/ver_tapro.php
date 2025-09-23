<?php
session_start();
$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;
include("../config.php");

// Recibe el id del proyecto por GET
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// Si no hay proyecto seleccionado y NO es admin, muestra mensaje y termina
if ($project_id == 0 && !$is_admin) {
    echo "<main class='contenido'><h2>No se seleccionó ningún proyecto.</h2></main>";
    exit;
}

// Consulta para admin: muestra todas las tareas
if ($is_admin && $project_id == 0) {
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
} else {
    // Consulta normal: solo tareas del proyecto y usuario
    $result = $conn->query("SELECT * FROM tasks WHERE creator_id = $user_id AND project_id = $project_id ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Tareas Guardadas</title>
    <link rel="stylesheet" href="../css/ver_tapro.css" />
</head>
<body>
    <main class="contenido">
        <h2>
            <?php
            if ($is_admin && $project_id == 0) {
                echo "Todas las tareas";
            } else {
                echo "Tareas del Proyecto";
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
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description_md']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['prioridad']) ?></td>
                <td><?= htmlspecialchars($row['start_date']) ?></td>
                <td><?= htmlspecialchars($row['due_date']) ?></td>
                <td>
                    <?php if(!empty($row['archivo'])): ?>
                        <a href="uploads/<?= urlencode($row['archivo']) ?>" target="_blank">Ver archivo</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <div style="margin-top:20px;">
            <button onclick="window.location.href='asignar_proyectos.php'">Crear nueva tarea</button>
            <button onclick="window.location.href='inico_Admin.php'">Volver</button>
        </div>
    </main>
</body>
</html>