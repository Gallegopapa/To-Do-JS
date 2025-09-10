<?php
session_start();
$user_id = $_SESSION['user_id'];
include("config.php");

// obtener tareas principales
$sql = "SELECT * FROM tasks WHERE parent_task_id IS NULL AND creator_id = $user_id ORDER BY created_at DESC";
$tareas = $conn->query($sql);

// función para listar subtareas recursivas
function listarSubtareas($conn, $parent_id, $nivel=1, $user_id) {
    $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id AND creator_id = $user_id ORDER BY created_at ASC";
    $result = $conn->query($sql);
    while($s = $result->fetch_assoc()) {
        echo "<div class='subtarea' style='margin-left:".($nivel*20)."px'>";
        echo "↳ <b>{$s['title']}</b> <small>{$s['status']}</small>";
        echo " <a href='editar.php?id={$s['id']}'>✏️</a>";
        echo " <a href='eliminar.php?id={$s['id']}' onclick=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\">❌</a>";
        echo "</div>";
        listarSubtareas($conn, $s['id'], $nivel+1, $user_id);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Vista de Tareas</title>
    <link rel="stylesheet" href="css/vista_tarea.css" />
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
            <a href="tareas.php">Agregar Tareas</a>
        <a href="vista_tareas.php">Ver Tareas</a>
        <a class="cerrar_sesion" href="logout.php">Cerrar sesión</a>
        </nav>
    </div>
</header>

<main class="contenido">
    <h2>Lista de Tareas</h2>
    <?php while($t = $tareas->fetch_assoc()): ?>
        <div class="tarea">
            <b><?= $t['title'] ?></b> <small><?= $t['status'] ?></small>
            <a href="editar.php?id=<?= $t['id'] ?>">✏️</a>
            <a href="eliminar.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')">❌</a>
        </div>
        <?php listarSubtareas($conn, $t['id'], 1, $user_id); ?>
    <?php endwhile; ?>
</main>
</body>
</html>
