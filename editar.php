<?php
include("config.php");

$id = $_GET['id'];
$tarea = $conn->query("SELECT * FROM tasks WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $status = $_POST['status'];

    $sql = "UPDATE tasks SET title='$title', status='$status' WHERE id=$id";
    $conn->query($sql);

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="../css/inicio.css" />
</head>
<body>
<h2>✏️ Editar Tarea</h2>
<form method="POST">
    <label>Título:</label><br>
    <input type="text" name="title" value="<?= $tarea['title'] ?>" required><br><br>

    <label>Estado:</label><br>
    <select name="status">
        <option value="pendiente" <?= $tarea['status']=="pendiente"?"selected":"" ?>>Pendiente</option>
        <option value="en progreso" <?= $tarea['status']=="en progreso"?"selected":"" ?>>En progreso</option>
        <option value="completada" <?= $tarea['status']=="completada"?"selected":"" ?>>Completada</option>
    </select><br><br>

    <button type="submit">Actualizar</button>
</form>
</body>
</html>
