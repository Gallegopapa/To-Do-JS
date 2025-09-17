<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID de proyecto no especificado.";
    exit;
}

$proyecto_id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descripcion = $conn->real_escape_string($_POST['description']);
    $estado = isset($_POST['is_archived']) ? 1 : 0;

    $sql_update = "
        UPDATE projects 
        SET description = '$descripcion', 
            is_archived = $estado, 
            updated_at = NOW() 
        WHERE id = $proyecto_id
    ";

    if ($conn->query($sql_update)) {
        header("Location: proyectos_admin.php");
        exit;
    } else {
        echo "Error al actualizar el proyecto: " . $conn->error;
    }
}

$sql = "SELECT name, description, is_archived FROM projects WHERE id = $proyecto_id";
$resultado = $conn->query($sql);

if ($resultado->num_rows === 0) {
    echo "Proyecto no encontrado.";
    exit;
}

$proyecto = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Proyecto</title>
  <link rel="stylesheet" href="../css/css_proyectos.css">
</head>
<body>
  <div class="container">
    <h2>Editar Proyecto: <?= htmlspecialchars($proyecto['name']) ?></h2>
    <form method="post">
      <label for="description">Descripción:</label><br>
      <textarea name="description" rows="5" cols="50"><?= htmlspecialchars($proyecto['description']) ?></textarea><br><br>

      <label>
        <input type="checkbox" name="is_archived" <?= $proyecto['is_archived'] ? 'checked' : '' ?>>
        Marcar como Inactivo
      </label><b
