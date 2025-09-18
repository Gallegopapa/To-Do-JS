<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "ID de proyecto no especificado o inválido.";
    exit;
}

$proyecto_id = intval($_GET['id']);
$errors = [];

//procesa envio
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descripcion = isset($_POST['description']) ? trim($_POST['description']) : '';
    $estado = isset($_POST['is_archived']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE projects SET description = ?, is_archived = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt) {
        $errors[] = "Error en la base de datos (prepare): " . $conn->error;
    } else {
        $stmt->bind_param("sii", $descripcion, $estado, $proyecto_id);
        if (!$stmt->execute()) {
            $errors[] = "Error al actualizar el proyecto: " . $stmt->error;
        } else {
            //redirige al listado
            header("Location: proyectos_admin.php");
            exit;
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT name, description, is_archived FROM projects WHERE id = ?");
if (!$stmt) {
    echo "Error en la base de datos (prepare select): " . $conn->error;
    exit;
}
$stmt->bind_param("i", $proyecto_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    echo "Error al obtener datos: " . $stmt->error;
    exit;
}
if ($result->num_rows === 0) {
    echo "Proyecto no encontrado.";
    exit;
}
$proyecto = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Proyecto</title>
  <link rel="stylesheet" href="../css/editar_proyectos.css">
</head>
<body>
  <div class="container">
    <h2>Editar Proyecto: <?= htmlspecialchars($proyecto['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></h2>

    <?php if (!empty($errors)): ?>
      <div class="errors" style="color: darkred;">
        <?php foreach ($errors as $err): ?>
          <p><?= htmlspecialchars($err, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label for="description">Descripción:</label><br>
      <textarea id="description" name="description" rows="5" cols="60"><?= htmlspecialchars($proyecto['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></textarea><br><br>

      <label>
        <input type="checkbox" name="is_archived" <?= ($proyecto['is_archived'] ? 'checked' : '') ?>>
        Marcar como Inactivo
      </label>
      <br><br>

      <button type="submit">Actualizar Proyecto</button>
      <a href="proyectos_admin.php" style="margin-left:10px;">Cancelar</a>
    </form>
  </div>
</body>
</html>
