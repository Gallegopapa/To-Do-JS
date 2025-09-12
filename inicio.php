<?php
session_start();
include("config.php");

// lleva al login si no hay sesion
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

// filtros
$where = "parent_task_id IS NULL AND creator_id = $user_id";
if (!empty($_GET["etiqueta"])) {
    $etiqueta = $conn->real_escape_string($_GET["etiqueta"]);
    $where .= " AND etiquetas LIKE '%$etiqueta%'";
}
if (!empty($_GET["prioridad"])) {
    $prioridad = $conn->real_escape_string($_GET["prioridad"]);
    $where .= " AND prioridad = '$prioridad'";
}
if (!empty($_GET["estado"])) {
    $estado = $conn->real_escape_string($_GET["estado"]);
    $where .= " AND status = '$estado'";
}
if (!empty($_GET["fecha_inicio"])) {
    $fi = $conn->real_escape_string($_GET["fecha_inicio"]);
    $where .= " AND start_date >= '$fi'";
}
if (!empty($_GET["fecha_vencimiento"])) {
    $fv = $conn->real_escape_string($_GET["fecha_vencimiento"]);
    $where .= " AND due_date <= '$fv'";
}

//consulta tareas principales filtradas
$sql = "SELECT * FROM tasks WHERE $where ORDER BY created_at DESC";
$tareas = $conn->query($sql);

//funcion para mostrar subtareas (recursiva, ahora con <li>)
function listarSubtareasVista($conn, $parent_id, $nivel=1, $user_id) {
    $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id AND creator_id = $user_id ORDER BY created_at ASC";
    $result = $conn->query($sql);
    while($s = $result->fetch_assoc()) {
        echo "<li class='subtarea-item' style='margin-left:".($nivel*15)."px'>";
        echo "↳ <b>".htmlspecialchars($s['title'])."</b> <small>[".htmlspecialchars($s['status'])."]</small><br>";
        echo "<span>Prioridad:</span> ".htmlspecialchars($s['prioridad'])." | ";
        echo "<span>".htmlspecialchars($s['etiquetas'])."</span> | ";
        echo "<span>Inicio: ".($s['start_date'] ?: '-')." / Vence: ".($s['due_date'] ?: '-')."</span>";
        echo " <a href='editar.php?id={$s['id']}'><img src='svg/lucide--edit(1).svg'></a>";
        echo " <a href='eliminar.php?id={$s['id']}' onclick=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\"><img src='svg/material-symbols--close (1).svg'></a>";
        echo "</li>";
        listarSubtareasVista($conn, $s['id'], $nivel+1, $user_id);
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
      <div class="btn-menu"><label for="btn-menu">☰</label></div>
      <div class="logo"><h1>GESTOR TAREAS</h1></div>
      <nav class="menu">
        <a href="vista_tareas.php">Ver Tareas</a>
        <a href="tareas.php">Agregar Tareas</a>
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
        <h1 class="bienvenida">
          Bienvenid@ a tu gestor de tareas, 
          <span class="user_name"><?= htmlspecialchars($user_name) ?></span>
        </h1>

        <!-- formulario de filtros -->
        <form method="GET" class="filtro-form">
          <p>Etiqueta:</p>
          <input type="text" name="etiqueta" value="<?= htmlspecialchars($_GET['etiqueta'] ?? '') ?>" placeholder="Trabajo, estudio...">

          <p>Prioridad:</p>
          <select name="prioridad">
            <option value="">Todas</option>
            <option value="Alta" <?= (($_GET['prioridad'] ?? '')=="Alta")?'selected':''; ?>>Alta</option>
            <option value="Media" <?= (($_GET['prioridad'] ?? '')=="Media")?'selected':''; ?>>Media</option>
            <option value="Baja" <?= (($_GET['prioridad'] ?? '')=="Baja")?'selected':''; ?>>baja</option>
          </select>

          <p>Estado:</p>
          <select name="estado">
            <option value="">Todos</option>
            <option value="pendiente" <?= (($_GET['estado'] ?? '')=="pendiente")?'selected':''; ?>>Pendiente</option>
            <option value="en progreso" <?= (($_GET['estado'] ?? '')=="en progreso")?'selected':''; ?>>En Progreso</option>
            <option value="completada" <?= (($_GET['estado'] ?? '')=="completada")?'selected':''; ?>>Completada</option>
          </select>
          <br>
          <p>Inicio:</p>
          <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">

          <p>Vence:</p>
          <input type="date" name="fecha_vencimiento" value="<?= htmlspecialchars($_GET['fecha_vencimiento'] ?? '') ?>">

          <button class="buscar" type="submit">Buscar</button>

          <button class="limpiar" onclick="window.location.href='inicio.php'">
            Limpiar
          </button>

        </form>
      </nav>
      <label for="btn-menu">✘</label>
    </div>
  </div>

  <main class="contenido" style="padding:20px; color:white;">
      <h2>Resultados de tus Tareas</h2>
      <hr><br>
      <ul class="tareas-lista">
        <?php while($t = $tareas->fetch_assoc()): ?>
            <li class="tarea-item">
                <b><?= htmlspecialchars($t['title']) ?></b> 
                <small><?= htmlspecialchars($t['status']) ?></small><br>
                <span>Prioridad:</span> <?= htmlspecialchars($t['prioridad']) ?> | 
                <span><?= htmlspecialchars($t['etiquetas']) ?></span> | 
                <span>Inicio: <?= $t['start_date'] ?: '-' ?> / Vence: <?= $t['due_date'] ?: '-' ?></span>
                <br>
                <a href="editar.php?id=<?= $t['id'] ?>"><img src="svg/lucide--edit(1).svg" alt=""></a>
            <a href="eliminar.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')"><img src="svg/material-symbols--close (1).svg" alt=""></a>
            </li>
            <?php listarSubtareasVista($conn, $t['id'], 1, $user_id); ?>
        <?php endwhile; ?>
      </ul>
  </main>
</body>
</html>
