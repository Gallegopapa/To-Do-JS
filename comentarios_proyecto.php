<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$is_admin = isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin";

if (!isset($_GET['project_id'])) {
    die("No se especificó proyecto.");
}
$project_id = intval($_GET['project_id']);

// Verificar que el usuario tenga acceso al proyecto (sea propietario, asignado o admin)
if ($is_admin) {
    // Los administradores pueden ver comentarios de cualquier proyecto
    $sql_project = "SELECT * FROM projects WHERE id = $project_id";
} else {
    // Los usuarios normales solo pueden ver proyectos donde son propietarios o asignados
    $sql_project = "SELECT * FROM projects 
                    WHERE id = $project_id 
                    AND (owner_id = $user_id OR assigned_to = $user_id)";
}
$result_project = $conn->query($sql_project);
if (!$result_project || $result_project->num_rows == 0) {
    if ($is_admin) {
        die("Proyecto no encontrado.");
    } else {
        die("Proyecto no encontrado o no tienes acceso.");
    }
}
$proyecto = $result_project->fetch_assoc();

// Verificar si existe la tabla project_comments, si no crearla
$table_check = $conn->query("SHOW TABLES LIKE 'project_comments'");
if ($table_check->num_rows == 0) {
    $create_table = "CREATE TABLE project_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($create_table);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $conn->real_escape_string($_POST['comment']);
    if (!empty($comment)) {
        $sql_insert = "INSERT INTO project_comments (project_id, user_id, comment) VALUES ($project_id, $user_id, '$comment')";
        $conn->query($sql_insert);
        header("Location: comentarios_proyecto.php?project_id=$project_id");
        exit;
    }
}

$sql_comments = "SELECT c.comment, c.created_at, u.name 
                FROM project_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.project_id = $project_id
                ORDER BY c.created_at ASC";
$comentarios = $conn->query($sql_comments);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comentarios - <?= htmlspecialchars($proyecto['name']) ?></title>
    <link rel="stylesheet" href="css/comentarios_proyecto.css">
</head>
<body>
    <div class="card">
        <h2>Comentarios del Proyecto</h2>
        
        <div class="proyecto-info">
            <h3><?= htmlspecialchars($proyecto['name']) ?></h3>
            <p><?= htmlspecialchars($proyecto['description']) ?></p>
            <small><strong>Creado:</strong> <?= $proyecto['created_at'] ?></small>
        </div>
        
        <hr>
        
        <div class="comentarios">
            <?php if ($comentarios && $comentarios->num_rows > 0): ?>
                <?php while($c = $comentarios->fetch_assoc()): ?>
                    <div class="comentario">
                        <b><?= htmlspecialchars($c['name']) ?>:</b><br>
                        <?= nl2br(htmlspecialchars($c['comment'])) ?><br>
                        <small><?= $c['created_at'] ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-comments">
                    <p>No hay comentarios aún en este proyecto.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <h3>Agregar un comentario</h3>
        <form method="POST">
            <textarea name="comment" placeholder="Escribe tu comentario sobre este proyecto..." required></textarea>
            <div>
                <?php if ($is_admin): ?>
                    <a href="Admin/proyectos_Admin.php" class="btn-volver">Volver a Proyectos</a>
                <?php else: ?>
                    <a href="mis_proyectos.php" class="btn-volver">Volver a Proyectos</a>
                <?php endif; ?>
                <button type="submit">Comentar</button>
            </div>
        </form>
    </div>
</body>
</html>