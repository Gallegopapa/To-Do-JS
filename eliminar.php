<?php
include("config.php");

$id = $_GET['id'];

// Borrar subtareas primero
$conn->query("DELETE FROM tasks WHERE parent_task_id=$id");
// Borrar tarea principal
$conn->query("DELETE FROM tasks WHERE id=$id");

header("Location: index.php");
exit();
