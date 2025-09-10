<?php
session_start();
include("config.php");

//lleva al login si el usuario no ha iniciado sesion
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

// consulta tareas principales
$sql = "SELECT * FROM tasks WHERE parent_task_id IS NULL AND creator_id = $user_id ORDER BY created_at DESC";
$tareas = $conn->query($sql);


// función para mostrar subtareas (recursiva)
function listarSubtareasMenu($conn, $parent_id, $nivel=1, $user_id) {
     $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id AND creator_id = $user_id ORDER BY created_at ASC";
    $result = $conn->query($sql);
    while($s = $result->fetch_assoc()) {
        echo "<li style='margin-left:".($nivel*15)."px'>↳ ".htmlspecialchars($s['title'])."</li>";
        listarSubtareasMenu($conn, $s['id'], $nivel+1, $user_id); 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/inicio.css" />
    <title>Gestor De Tareas</title>
  </head>
  <body>
    <header class="header">
      <div class="container">
        <div class="btn-menu">
          <label for="btn-menu">☰</label>
        </div>
        <div class="logo">
          <h1>GESTOR TAREAS</h1>
        </div>
        <nav class="menu">
          <a href="tareas.php">Ver / Agregar Tareas</a>
          <a href="#">Gestionar Proyectos</a>
          <a class="cerrar_sesion" href="logout.php">Cerrar sesión</a>
        </nav>
      </div>
    </header>
    <div class="capa"></div>
    <input type="checkbox" id="btn-menu" />
    <div class="container-menu">
        <div class="cont-menu">
            <nav>
            <h1 class="bienvenida">Bienvenid@ a tu gestor de tareas, <span class="user_name"><?= htmlspecialchars($user_name) ?></span></h1>

                <h2 style="margin-top:15px; color: #fff;">Tus Tareas</h2>
                <hr><br>
                <ul>
                <?php while($t = $tareas->fetch_assoc()): ?>
                  <li>
                    <?= htmlspecialchars($t['title']) ?> (<?= htmlspecialchars($t['status']) ?>)
                    <?php listarSubtareasMenu($conn, $t['id'], 1, $user_id); ?>
                  </li>
                <?php endwhile; ?>
                </ul>
            </nav>
            <label for="btn-menu">✘</label>
        </div>
    </div>
  </body>

</html>
