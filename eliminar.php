<?php
session_start();
include("config.php");

$id = $_GET['id'];

//elimina subtareas y tarea principal
$conn->query("DELETE FROM tasks WHERE parent_task_id=$id");
$conn->query("DELETE FROM tasks WHERE id=$id");

// Verifica el rol desde la sesión
if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
    header("Location: Admin/inico_Admin.php");
} else {
    header("Location: inicio.php");
}
exit();
