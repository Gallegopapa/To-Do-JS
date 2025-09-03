<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";

    $sql = "INSERT INTO tasks (title, status, parent_task_id, created_at) 
            VALUES ('$title', '$status', $parent, NOW())";
    $conn->query($sql);

    header("Location: index.php");
    exit();
}

$tareas = $conn->query("SELECT id, title FROM tasks WHERE parent_task_id IS NULL");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Tarea</title>
    <link rel="stylesheet" href="css/inicio.css" />
</head>
<body>
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
        <option value="">Ninguna (Tarea principal)</option>
        <?php while($t = $tareas->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>"><?= $t['title'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Guardar</button>
</form>
</body>
</html>
