<?php
include("config.php");

$id = $_GET['id'];
$conn->query("DELETE FROM tasks WHERE parent_task_id=$id");
$conn->query("DELETE FROM tasks WHERE id=$id");

header("Location: index.php");
exit();
