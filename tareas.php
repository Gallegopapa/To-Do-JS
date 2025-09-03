<?php
include("config.php");

// INSERTAR tarea si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";

    $sql = "INSERT INTO tasks (title, status, parent_task_id, creator_id, created_at) 
        VALUES ('$title', '$status', $parent, 1, NOW())";

    $conn->query($sql);
}

// Obtener tareas raíz
$sql = "SELECT * FROM tasks WHERE parent_task_id IS NULL ORDER BY created_at DESC";
$tareas = $conn->query($sql);

// Función recursiva para subtareas
function listarSubtareas($conn, $parent_id, $nivel=1) {
    $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id ORDER BY created_at ASC";
    $result = $conn->query($sql);
    while($s = $result->fetch_assoc()) {
        echo "<div class='subtarea' style='margin-left:".($nivel*20)."px'>";
        echo "↳ <b>{$s['title']}</b> <small>({$s['status']})</small>";
        echo " <a href='editar.php?id={$s['id']}'>✏️</a>";
        echo " <a href='eliminar.php?id={$s['id']}' onclick=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\">🗑️</a>";
        echo "</div>";
        listarSubtareas($conn, $s['id'], $nivel+1);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>GESTOR DE TAREAS</title>
    <link rel="stylesheet" href="../css/tareas.css" />
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
            <a href="index.php">Ver / Agregar Tareas</a>
        </nav>
    </div>
</header>

<main class="contenido">
    <h2>📋 Lista de Tareas</h2>
    <?php while($t = $tareas->fetch_assoc()): ?>
        <div class="tarea">
            <b><?= $t['title'] ?></b> <small>(<?= $t['status'] ?>)</small>
            <a href="editar.php?id=<?= $t['id'] ?>">✏️</a>
            <a href="eliminar.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')"><img src="../img/material-symbols--close-rounded.svg" alt=""></a>
        </div>
        <?php listarSubtareas($conn, $t['id']); ?>
    <?php endwhile; ?>

    <hr>
    <h2>Crear Nueva Tarea</h2>
    <form method="POST">
        <label>Título:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Estado:</label><br>
        <select name="status">
            <option value="pendiente">Pendiente</option>
            <option value="en progreso">En progreso</option>
            <option value="completada">Completada</option>
        </select><br><br>

        <label>Subtarea de:</label><br>
        <select name="parent_task_id">
            <option value="">-- Ninguna (Tarea principal) --</option>
            <?php
            $padres = $conn->query("SELECT id, title FROM tasks WHERE parent_task_id IS NULL");
            while($p = $padres->fetch_assoc()):
            ?>
                <option value="<?= $p['id'] ?>"><?= $p['title'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Guardar</button>
    </form>
</main>
</body>
</html>
