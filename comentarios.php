<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

if (!isset($_GET['task_id'])) {
    die("No se especificó tarea.");
}
$task_id = intval($_GET['task_id']);

$sql_task = "SELECT * FROM tasks WHERE id = $task_id";
$result_task = $conn->query($sql_task);
if (!$result_task || $result_task->num_rows == 0) {
    die("Tarea no encontrada.");
}
$tarea = $result_task->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $conn->real_escape_string($_POST['comment']);
    if (!empty($comment)) {
        $sql_insert = "INSERT INTO task_comments (task_id, user_id, comment) VALUES ($task_id, $user_id, '$comment')";
        $conn->query($sql_insert);
        header("Location: comentarios.php?task_id=$task_id");
        exit;
    }
}

$sql_comments = "SELECT c.comment, c.created_at, u.name 
                FROM task_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.task_id = $task_id
                ORDER BY c.created_at ASC";
$comentarios = $conn->query($sql_comments);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comentarios - <?= htmlspecialchars($tarea['title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e9ecef;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .card {
            background: #fff;
            max-width: 700px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 20px 25px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 5px;
        }
        .btn-volver {
            display: inline-block;
            margin: 10px 0;
            background: #0066cc;
            color: #fff;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-volver:hover {
            background: #5a6268;
        }
        .comentarios {
            margin: 20px 0;
        }
        .comentario {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal 
        }
        .comentario:last-child {
            border-bottom: none;
        }
        .comentario small {
            color: #777;
            font-size: 12px;
        }
        form textarea {
            width: 100%;
            height: 70px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: none;
            margin-bottom: 10px;
        }
        form button {
            background: #0066cc;
            border: none;
            color: #fff;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        form button:hover {
            background: #004c99;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Comentarios para: <?= htmlspecialchars($tarea['title']) ?></h2>
        
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
                        <p><i>No hay comentarios aún.</i></p>
                        <?php endif; ?>
                    </div>
                    
                    <h3>Agregar un comentario</h3>
                    <form method="POST">
                        <textarea name="comment" placeholder="Escribe tu comentario..." required></textarea>
                        <a href="<?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'admin/inico_Admin.php' : 'inicio.php' ?>" class="btn-volver">Volver a Inicio</a>
            <button type="submit">Comentar</button>
        </form>
    </div>
</body>
</html>
