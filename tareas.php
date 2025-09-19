<?php
session_start();
$user_id = $_SESSION['user_id'];
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";
    $etiquetas = $_POST['etiquetas'];
    $prioridad = $_POST['prioridad'];
    $start_date = !empty($_POST['start_date']) ? "'".$_POST['start_date']."'" : "NULL";
    $due_date = !empty($_POST['due_date']) ? "'".$_POST['due_date']."'" : "NULL";

    $archivo_nombre = "";
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivo_tmp = $_FILES['archivo']['tmp_name'];
        $archivo_nombre = basename($_FILES['archivo']['name']);
        $carpeta_destino = "uploads/";

        if (!file_exists($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        move_uploaded_file($archivo_tmp, $carpeta_destino . $archivo_nombre);
    }

    $sql = "INSERT INTO tasks (title, description_md, status, parent_task_id, creator_id, etiquetas, prioridad, start_date, due_date, archivo, created_at) 
            VALUES ('$title', '$description', '$status', $parent, $user_id, '$etiquetas', '$prioridad', $start_date, $due_date, '$archivo_nombre', NOW())";
    $conn->query($sql);

    header("Location: inicio.php");
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
        <div class="logo"><a href="inicio.php"><h1>GESTOR TAREAS</h1></a></div>
        <nav class="menu">
        <a href="mis_proyectos.php">Gestionar proyectos</a>
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
            <option value="Pendiente">Pendiente</option>
            <option value="En_progreso">En progreso</option>
            <option value="Completada">Completada</option>
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

        <label>Etiquetas (separadas por coma):</label>
        <input type="text" name="etiquetas">

        <label>Prioridad:</label>
        <select name="prioridad">
            <option value="Alta">Alta</option>
            <option value="Media" selected>Media</option>
            <option value="Baja">Baja</option>
        </select>

        <label>Fecha de inicio:</label>
        <input type="date" name="start_date">

        <label>Fecha de vencimiento:</label>
        <input type="date" name="due_date">

        <label>Archivo adjunto:</label><br>
        <input type="file" name="archivo" id="archivo" style="display:none">
        <button type="button" onclick="document.getElementById('archivo').click()">Seleccionar archivo</button>
        <span id="nombreArchivo"></span>

        <br><br>
        <button type="submit">Guardar</button>
    </form>
    <script>

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