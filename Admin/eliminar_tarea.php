<?php
session_start();
include("../config.php");

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

// Recibe el id de la tarea
if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);

    if ($is_admin) {
        // 🔹 Si es admin puede borrar cualquier tarea
        $sql = "DELETE FROM admin_tasks WHERE id = $task_id";
    } else {
        // 🔹 Si es usuario normal solo puede borrar las suyas
        $sql = "DELETE FROM admin_tasks WHERE id = $task_id AND creator_id = $user_id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: ver_tapro.php");
        exit;
    } else {
        echo "Error al eliminar la tarea: " . $conn->error;
    }
} else {
    echo "No se especificó ninguna tarea para eliminar.";
}
?>
    