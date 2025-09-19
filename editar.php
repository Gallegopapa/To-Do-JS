<?php
session_start();
include("config.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tarea = $conn->query("SELECT * FROM tasks WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = isset($_POST['description']) ? $_POST['description'] : ''; // evita warning
    $status = $_POST['status'];

    $sql = "UPDATE tasks 
            SET title='$title', description_md='$description', status='$status' 
            WHERE id=$id";
    $conn->query($sql);

    // ✅ Redirección según rol
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
        header("Location: Admin/inico_Admin.php");
    } else {
        header("Location: inicio.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/editar.css"/>
    <title>Editar Tarea</title>
</head>
<body>
    <form method="POST">
        <h2>Editar Tarea</h2>
        
        <label>Título:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($tarea['title']) ?>" required><br><br>


        <label>Estado:</label><br>
        <select name="status">
    <option value="Pendiente" <?= $tarea['status']=="Pendiente"?"selected":"" ?>>Pendiente</option>
    <option value="En_progreso" <?= $tarea['status']=="En_progreso"?"selected":"" ?>>En progreso</option>
    <option value="Completada" <?= $tarea['status']=="Completada"?"selected":"" ?>>Completada</option>
</select>

        <button type="submit">Actualizar</button>
        <a href="inicio.php" class="btn-volver">Volver a Inicio</a>
    </form>
</body>
</html>
