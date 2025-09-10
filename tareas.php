<?php
session_start();
$user_id = $_SESSION['user_id'];
include("config.php");

// Insertar tarea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";

    $sql = "INSERT INTO tasks (title, description_md, status, parent_task_id, creator_id, created_at) 
            VALUES ('$title', '$description', '$status', $parent, $user_id, NOW())";
    $conn->query($sql);

    header("Location: vista_tareas.php"); // redirige a la nueva vista
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>GESTOR DE TAREAS</title>
    <link rel="stylesheet" href="css/tareas.css" />
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
        <a href="vista_tareas.php">Ver Tareas</a>
        <a href="tareas.php">Agregar Tareas</a>
        <a class="cerrar_sesion" href="logout.php">Cerrar sesión</a>
        </nav>
    </div>
</header>

<main class="contenido">
    <h2>Crear Nueva Tarea</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Título:</label>
        <input type="text" name="title" required>

        <label>Estado:</label>
        <select name="status">
            <option value="pendiente">Pendiente</option>
            <option value="en progreso">En progreso</option>
            <option value="completada">Completada</option>
        </select>

        <label>Subtarea de:</label>
        <select name="parent_task_id">
            <option value="">Ninguna (Tarea principal)</option>
            <?php
            $padres = $conn->query("SELECT id, title FROM tasks WHERE parent_task_id IS NULL AND creator_id = $user_id");
            while($p = $padres->fetch_assoc()):
            ?>
                <option value="<?= $p['id'] ?>"><?= $p['title'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Archivo adjunto:</label><br>
        <input type="file" name="archivo" id="archivo" style="display:none">
        <button type="button" onclick="document.getElementById('archivo').click()">Seleccionar archivo</button>
        <span id="nombreArchivo"></span>

        <br><br>
        <button type="submit">Guardar</button>
    </form>

    <script>
    // Mostrar nombre del archivo
    const inputArchivo = document.getElementById('archivo');
    const nombreArchivo = document.getElementById('nombreArchivo');
    inputArchivo.addEventListener('change', () => {
        if (inputArchivo.files.length > 0) {
            nombreArchivo.textContent = "Archivo seleccionado: " + inputArchivo.files[0].name;
        } else {
            nombreArchivo.textContent = "";
        }
    });
    </script>
</main>
</body>
</html>
