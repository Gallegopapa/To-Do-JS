<?php
session_start();
$user_id = $_SESSION['user_id'];
include("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $parent = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : "NULL";
    $etiquetas = $_POST['etiquetas'];
    $prioridad = $_POST['prioridad'];
    $start_date = !empty($_POST['start_date']) ? "'".$_POST['start_date']."'" : "NULL";
    $due_date = !empty($_POST['due_date']) ? "'".$_POST['due_date']."'" : "NULL";

    // ✅ Capturar project_id que viene del formulario
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;

    // Manejo del archivo
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

    // ✅ Insertar en la tabla admin_tasks incluyendo project_id
    $sql = "INSERT INTO admin_tasks 
        (title, description_md, status, parent_task_id, creator_id, etiquetas, priority, prioridad, start_date, due_date, archivo, project_id, created_at) 
        VALUES 
        ('$title', '$description', '$status', $parent, $user_id, '$etiquetas', '$prioridad', '$prioridad', $start_date, $due_date, '$archivo_nombre', $project_id, NOW())";

    if ($conn->query($sql)) {
        $task_id = $conn->insert_id; // id de la tarea recién creada

        // 🔹 Obtener los usuarios asignados a este proyecto
        $usuarios_sql = "SELECT user_id FROM proyectos_usuarios WHERE project_id = $project_id";
        $resU = $conn->query($usuarios_sql);

        while ($u = $resU->fetch_assoc()) {
            $asignado = $u['user_id'];

            // ✅ Actualizar la tarea para reflejar el usuario asignado
            // 👉 Si quieres que una tarea solo tenga un usuario, esto basta.
            $update = "UPDATE admin_tasks SET assigned_to = $asignado WHERE id = $task_id";
            $conn->query($update);

            // ⚠️ Si necesitas que cada usuario tenga su propia copia de la tarea,
            // aquí en vez de UPDATE se debería hacer un INSERT duplicando la tarea.
        }
    }

    header("Location: ver_tapro.php?project_id=$project_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Tarea</title>
    <link rel="stylesheet" href="../css/asign_tareas.css" />
</head>
<body>
<main class="contenido">
    <h2>Crear Nueva Tarea (Admin)</h2>
    <form method="POST" enctype="multipart/form-data">
        <!-- ✅ Campo oculto que asegura que se envía el project_id -->
        <input type="hidden" name="project_id" value="<?= isset($_GET['project_id']) ? intval($_GET['project_id']) : 0 ?>">

        <label>Título:</label>
        <input type="text" name="title" required>

        <label>Descripción:</label>
        <textarea name="description" rows="4" placeholder="Escribe la descripción de la tarea..."></textarea>

        <label>Estado:</label>
        <select name="status">
            <option value="En progreso">En progreso</option>
            <option value="Completada">Completada</option>
            <option value="Pendiente">Pendiente</option>
        </select>

        <label>Subtarea de:</label>
        <select name="parent_task_id">
            <option value="">Ninguna (Tarea principal)</option>
            <?php
            $padres = $conn->query("SELECT id, title FROM admin_tasks WHERE parent_task_id IS NULL AND creator_id = $user_id");
            while($p = $padres->fetch_assoc()):
            ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Etiquetas (separadas por coma):</label>
        <input type="text" name="etiquetas">

        <label>Prioridad (simple):</label>
        <select name="prioridad">
            <option value="Baja">Baja</option>
            <option value="Media" selected>Media</option>
            <option value="Alta">Alta</option>
            <option value="Urgente">Urgente</option>
        </select>

        <label>Fecha de inicio:</label>
        <input type="date" name="start_date" required>

        <label>Fecha de vencimiento:</label>
        <input type="date" name="due_date" min="<?= date('Y-m-d') ?>" required>

        <label>Archivo adjunto:</label><br>
        <input type="file" name="archivo" id="archivo" style="display:none">
        <button type="button" onclick="document.getElementById('archivo').click()">Seleccionar archivo</button>
        <span id="nombreArchivo"></span>

        <br><br>
        <div class="extra-buttons">
            <button type="submit">Guardar</button>
            <a href="inico_Admin.php"><button type="button">Volver</button></a>
            <a href="ver_tapro.php?project_id=<?= isset($_GET['project_id']) ? intval($_GET['project_id']) : 0 ?>"><button type="button">Ver tareas asignadas</button></a>
        </div>
    </form>
</main>
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
</body>
</html>
