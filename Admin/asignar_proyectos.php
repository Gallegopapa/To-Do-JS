<?php
session_start();
include("../config.php");

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$yo = $conn->query("SELECT role, name, profile_pic FROM users WHERE id=".(int)$_SESSION["user_id"])->fetch_assoc();
if (!$yo || $yo["role"] !== "admin") {
    die("Acceso restringido. Solo admins pueden entrar.");
}   

// Captura el project_id desde GET o POST
$project_id = 0;
if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);
} elseif (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
} elseif (isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);
}

// Función para convertir valores opcionales a enteros seguros (0 si es null)
function safeInt($value) {
    return isset($value) && $value !== '' ? intval($value) : 0;
}

// Manejo del POST para crear la tarea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'Pendiente';
    $parent = safeInt($_POST['parent_task_id'] ?? null);
    $assigned_to = safeInt($_POST['assigned_to'] ?? null);
    $etiquetas = $_POST['etiquetas'] ?? '';
    $prioridad = $_POST['prioridad'] ?? 'Media';
    $start_date = $_POST['start_date'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

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

    // Insertar la tarea vinculada al proyecto y opcionalmente a un usuario
    $sql = "INSERT INTO admin_tasks 
        (title, description_md, status, parent_task_id, creator_id, assigned_to, etiquetas, priority, prioridad, start_date, due_date, archivo, project_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Bind seguro usando valores enteros reemplazando null por 0
    // 🔹 Nota importante: la última variable $project_id es int, por eso "i" al final
    $stmt->bind_param(
        "sssiisssssssi",
        $title,
        $description,
        $status,
        $parent,
        $user_id,
        $assigned_to,
        $etiquetas,
        $prioridad,
        $prioridad,
        $start_date,
        $due_date,
        $archivo_nombre,
        $project_id
    );

    if ($stmt->execute()) {
        header("Location: ver_tapro.php?project_id=$project_id");
        exit;
    } else {
        die("Error al crear la tarea: " . $stmt->error);
    }
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
        <input type="hidden" name="project_id" value="<?= $project_id ?>">

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
            $padres = $conn->query("SELECT id, title FROM admin_tasks WHERE parent_task_id = 0 AND creator_id = $user_id");
            if ($padres) {
                while($p = $padres->fetch_assoc()):
                    ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                    <?php
                endwhile;
            }
            ?>
        </select>

        <label>Asignar a usuario:</label>
        <select name="assigned_to">
            <option value="">Sin asignar</option>
            <?php
            $usuarios = $conn->query("SELECT u.id, u.name FROM users u INNER JOIN proyectos_usuarios pu ON u.id = pu.user_id WHERE pu.project_id = $project_id");
            if ($usuarios) {
                while($usuario = $usuarios->fetch_assoc()):
                    ?>
                    <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['name']) ?></option>
                    <?php
                endwhile;
            }
            ?>
        </select>

        <label>Etiquetas (separadas por coma):</label>
        <input type="text" name="etiquetas">

        <label>Prioridad:</label>
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
            <a href="ver_tapro.php?project_id=<?= $project_id ?>"><button type="button">Ver tareas asignadas</button></a>
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
