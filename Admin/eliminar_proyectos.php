<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Eliminar tareas relacionadas
    $conn->query("DELETE FROM tasks WHERE project_id = $id");

    // Eliminar el proyecto
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Proyecto eliminado correctamente.";
        } else {
            echo "No se encontró el proyecto con ese ID.";
        }
        // Puedes comentar el header para ver el mensaje
        header("Location: proyectos_admin.php?msg=proyecto_eliminado");
    } else {
        echo "Error al eliminar proyecto: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: proyectos_admin.php");
    exit;
}
