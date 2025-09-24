<?php
session_start();
$user_id = $_SESSION['user_id'];
include("../config.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos actuales de la tarea
$sql = "SELECT * FROM admin_tasks WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    echo "<main class='contenido'><h2>Tarea no encontrada.</h2></main>";
    exit;
}
$tarea = $result->fetch_assoc();

// Procesar edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description_md'];
    $status = $_POST['status'];
    $prioridad = $_POST['prioridad'];
    $start_date = !empty($_POST['start_date']) ? "'".$_POST['start_date']."'" : "NULL";
    $due_date = !empty($_POST['due_date']) ? "'".$_POST['due_date']."'" : "NULL";

    // Si se sube un nuevo archivo, reemplaza el anterior
    $archivo_nombre = $tarea['archivo'];
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivo_tmp = $_FILES['archivo']['tmp_name'];
        $archivo_nombre = basename($_FILES['archivo']['name']);
        $carpeta_destino = "uploads/";
        if (!file_exists($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }
        move_uploaded_file($archivo_tmp, $carpeta_destino . $archivo_nombre);
    }

    $sql = "UPDATE admin_tasks SET 
        title = '$title',
        description_md = '$description',
        status = '$status',
        prioridad = '$prioridad',
        start_date = $start_date,
        due_date = $due_date,
        archivo = '$archivo_nombre',
        updated_at = NOW()
        WHERE id = $id";
    $conn->query($sql);

    header("Location: ver_tapro.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="../css/asign_tareas.css" />
</head>
<body>
<main class="contenido">
    <h2>Editar Tarea</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Título:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($tarea['title']) ?>" required>

        <label>Descripción:</label>
        <textarea name="description_md" rows="4"><?= htmlspecialchars($tarea['description_md']) ?></textarea>

        <label>Estado:</label>
        <select name="status">
            <option value="Pendiente" <?= $tarea['status'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="En_progreso" <?= $tarea['status'] == 'En_progreso' ? 'selected' : '' ?>>En progreso</option>
            <option value="Completada" <?= $tarea['status'] == 'Completada' ? 'selected' : '' ?>>Completada</option>
        </select>

        <label>Prioridad:</label>
        <select name="prioridad">
            <option value="Alta" <?= $tarea['prioridad'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
            <option value="Media" <?= $tarea['prioridad'] == 'Media' ? 'selected' : '' ?>>Media</option>
            <option value="Baja" <?= $tarea['prioridad'] == 'Baja' ? 'selected' : '' ?>>Baja</option>
        </select>

        <label>Fecha de inicio:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($tarea['start_date'] ?? '') ?>" required>

        <label>Fecha de vencimiento:</label>
        <input type="date" name="due_date" value="<?= htmlspecialchars($tarea['due_date'] ?? '') ?>" required>

        <label>Archivo adjunto:</label><br>
        <input type="file" name="archivo" id="archivo" style="display:none">
        <button type="button" onclick="document.getElementById('archivo').click()">Seleccionar archivo</button>
        <span id="nombreArchivo"><?= !empty($tarea['archivo']) ? "Actual: " . htmlspecialchars($tarea['archivo']) : "" ?></span>

        <br><br>
        <button type="submit">Guardar cambios</button>
        <button type="button" onclick="window.location.href='ver_tapro.php'">Cancelar</button>
    </form>
</main>
<script>
const inputArchivo = document.getElementById('archivo');
const nombreArchivo = document.getElementById('nombreArchivo');
inputArchivo.addEventListener('change', () => {
    if (inputArchivo.files.length > 0) {
        nombreArchivo.textContent = "Nuevo archivo: " + inputArchivo.files[0].name;
    }
});
</script>
</body>
</html>