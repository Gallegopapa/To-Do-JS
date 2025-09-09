<?php
session_start();
$user_id = $_SESSION['user_id'];
include("config.php");

//esto inserta tarea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";

    $sql = "INSERT INTO tasks (title, description_md, status, parent_task_id, creator_id, created_at) 
        VALUES ('$title', '$description', '$status', $parent, $user_id, NOW())";

    $conn->query($sql);

    header("Location: tareas.php");
    exit;
}

//esto obtiene las tareas pincipales
$sql = "SELECT * FROM tasks WHERE parent_task_id IS NULL AND creator_id = $user_id ORDER BY created_at DESC";
$tareas = $conn->query($sql);


//funcion para subtareas
function listarSubtareas($conn, $parent_id, $nivel=1, $user_id) {
    $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id AND creator_id = $user_id ORDER BY created_at ASC";
    $result = $conn->query($sql);
    while($s = $result->fetch_assoc()) {
        echo "<div class='subtarea' style='margin-left:".($nivel*20)."px'>";
        echo "↳ <b>{$s['title']}</b> <small>({$s['status']})</small>";
        echo " <a href='editar.php?id={$s['id']}'>✏️</a>";
        echo " <a href='eliminar.php?id={$s['id']}' onclick=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\"></a>";
        echo "</div>";
        listarSubtareas($conn, $s['id'], $nivel+1, $user_id);
    }
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
            <!-- <label for="btn-menu">☰</label> -->
        </div>
        <div class="logo">
            <h1>GESTOR TAREAS</h1>
        </div>
        <nav class="menu">
            <a href="index.php">Ver / Agregar Tareas</a>
            <a href="inicio.php">Volver al inicio</a>
        </nav>
    </div>
</header>

<main class="contenido">
    <h2>Lista de Tareas</h2>
    <?php while($t = $tareas->fetch_assoc()): ?>
        <div class="tarea">
            <b><?= $t['title'] ?></b> <small>(<?= $t['status'] ?>)</small>
            <a href="editar.php?id=<?= $t['id'] ?>">✏️</a>
            <a href="eliminar.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')"><img src="img/material-symbols--close-rounded.svg" alt=""></a>
        </div>
        <?php listarSubtareas($conn, $t['id'], 1, $user_id); ?>
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

        <label>Descripción:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Subtarea de:</label><br>
        <select name="parent_task_id">
            <option value="">Ninguna (Tarea principal)</option>
            <?php
            $padres = $conn->query("SELECT id, title FROM tasks WHERE parent_task_id IS NULL");
            while($p = $padres->fetch_assoc()):
            ?>
                <option value="<?= $p['id'] ?>"><?= $p['title'] ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <form method="POST" enctype="multipart/form-data">
    <input type="file" name="archivo" id="archivo" style="display:none">
    <button type="button" onclick="document.getElementById('archivo').click()">Seleccionar archivo</button>
    <span id="nombreArchivo"></span>
    <br><br>
    <button type="submit">Guardar</button>
</form>
        <script>
        //esto muestra el nombre del archivo
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

    </form>
</main>
</body>
</html>
