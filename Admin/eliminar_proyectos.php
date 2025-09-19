<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 🔥 Primero eliminar posibles tareas ligadas al proyecto (si tu tabla tasks tiene FK con projects)
    $conn->query("DELETE FROM tasks WHERE project_id = $id");

    // 🔥 Luego eliminar el proyecto
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: proyectos_admin.php?msg=proyecto_eliminado");
    } else {
        echo "Error al eliminar proyecto: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: proyectos_admin.php");
    exit;
}
