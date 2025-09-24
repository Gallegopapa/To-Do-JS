<?php
session_start();
include("config.php");

//lleva al login si no hay sesion
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

$sql_profile = "SELECT profile_pic FROM users WHERE id = $user_id";
$result_profile = $conn->query($sql_profile);
$profile_pic = "img/default_profile.png";

if ($result_profile && $result_profile->num_rows > 0) {
    $row_profile = $result_profile->fetch_assoc();
    if (!empty($row_profile['profile_pic'])) {
        $profile_pic = $row_profile['profile_pic'];
    }
}

// filtros para tareas normales
$where_tasks = "parent_task_id IS NULL AND creator_id = $user_id";

// Verificar si la tabla admin_tasks existe
$table_check = $conn->query("SHOW TABLES LIKE 'admin_tasks'");
$admin_tasks_exists = $table_check && $table_check->num_rows > 0;

// filtros para tareas de proyectos (admin_tasks) - incluir proyectos donde el usuario tiene acceso
$where_admin = "project_id IN (SELECT id FROM projects WHERE owner_id = $user_id OR assigned_to = $user_id)";

if (!empty($_GET["etiqueta"])) {
    $etiqueta = $conn->real_escape_string($_GET["etiqueta"]);
    $where_tasks .= " AND etiquetas LIKE '%$etiqueta%'";
    $where_admin .= " AND etiquetas LIKE '%$etiqueta%'";
}
if (!empty($_GET["prioridad"])) {
    $prioridad = $conn->real_escape_string($_GET["prioridad"]);
    $where_tasks .= " AND prioridad = '$prioridad'";
    $where_admin .= " AND prioridad = '$prioridad'";
}
if (!empty($_GET["estado"])) {
    $estado = $conn->real_escape_string($_GET["estado"]);
    $where_tasks .= " AND status = '$estado'";
    $where_admin .= " AND status = '$estado'";
}
if (!empty($_GET["fecha_inicio"])) {
    $fi = $conn->real_escape_string($_GET["fecha_inicio"]);
    $where_tasks .= " AND start_date >= '$fi'";
    $where_admin .= " AND start_date >= '$fi'";
}
if (!empty($_GET["fecha_vencimiento"])) {
    $fv = $conn->real_escape_string($_GET["fecha_vencimiento"]);
    $where_tasks .= " AND due_date <= '$fv'";
    $where_admin .= " AND due_date <= '$fv'";
}

// Consulta unificada que incluye tanto tareas normales como tareas de proyectos
if ($admin_tasks_exists) {
    $sql = "
        (SELECT id, title, description_md, status, prioridad, etiquetas, start_date, due_date, archivo, created_at, 'normal' as tipo_tarea, NULL as project_id, NULL as project_name
         FROM tasks 
         WHERE $where_tasks)
        UNION ALL
        (SELECT at.id, at.title, at.description_md, at.status, 
         COALESCE(at.prioridad, 'Media') as prioridad, 
         at.etiquetas, at.start_date, at.due_date, at.archivo, at.created_at, 'proyecto' as tipo_tarea, at.project_id, p.name as project_name
         FROM admin_tasks at 
         JOIN projects p ON at.project_id = p.id
         WHERE $where_admin)
        ORDER BY created_at DESC";
} else {
    // Solo tareas normales si no existe la tabla admin_tasks
    $sql = "SELECT id, title, description_md, status, prioridad, etiquetas, start_date, due_date, archivo, created_at, 'normal' as tipo_tarea, NULL as project_id, NULL as project_name
            FROM tasks 
            WHERE $where_tasks
            ORDER BY created_at DESC";
}

$tareas = $conn->query($sql);

// Debug temporal específico para admin_tasks
if ($admin_tasks_exists) {
    echo "<!-- DEBUG ADMIN_TASKS -->";
    echo "<!-- User ID: $user_id -->";
    
    // Verificar si hay tareas en admin_tasks
    $debug_count = $conn->query("SELECT COUNT(*) as total FROM admin_tasks");
    $total_admin_tasks = $debug_count ? $debug_count->fetch_assoc()['total'] : 0;
    echo "<!-- Total tareas en admin_tasks: $total_admin_tasks -->";
    
    // Verificar tareas para proyectos accesibles por este usuario
    $debug_user = $conn->query("SELECT COUNT(*) as total FROM admin_tasks WHERE project_id IN (SELECT id FROM projects WHERE owner_id = $user_id OR assigned_to = $user_id)");
    $user_admin_tasks = $debug_user ? $debug_user->fetch_assoc()['total'] : 0;
    echo "<!-- Tareas admin_tasks para proyectos accesibles por user $user_id: $user_admin_tasks -->";
    
    // Mostrar proyectos accesibles
    $debug_projects = $conn->query("SELECT id, name FROM projects WHERE owner_id = $user_id OR assigned_to = $user_id LIMIT 5");
    if ($debug_projects && $debug_projects->num_rows > 0) {
        echo "<!-- Proyectos accesibles: ";
        while($proj = $debug_projects->fetch_assoc()) {
            echo "[ID:{$proj['id']}, name:'{$proj['name']}'] ";
        }
        echo " -->";
    }
    
    // Probar la consulta de admin_tasks por separado
    $test_admin = $conn->query("SELECT id, title, project_id FROM admin_tasks WHERE project_id IN (SELECT id FROM projects WHERE owner_id = $user_id OR assigned_to = $user_id) LIMIT 10");
    if ($test_admin) {
        echo "<!-- Tareas que deberían aparecer para user $user_id: " . $test_admin->num_rows . " -->";
        if ($test_admin->num_rows > 0) {
            echo "<!-- Detalles: ";
            while($task = $test_admin->fetch_assoc()) {
                echo "[ID:{$task['id']}, '{$task['title']}', project:{$task['project_id']}] ";
            }
            echo " -->";
        }
    } else {
        echo "<!-- Error en consulta admin_tasks: " . $conn->error . " -->";
    }
    echo "<!-- /DEBUG ADMIN_TASKS -->";
}

// función recursiva para mostrar subtareas
function listarSubtareasVista($conn, $parent_id, $nivel=1, $user_id) {
    $sql = "SELECT * FROM tasks WHERE parent_task_id = $parent_id AND creator_id = $user_id ORDER BY created_at ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<ul class='subtareas-lista nivel-$nivel'>";
        while($s = $result->fetch_assoc()) {
            echo "<li class='subtarea-item'>";
            echo "<div class='subtarea-header'>";
            echo "<b>".htmlspecialchars($s['title'])."</b> <small>".htmlspecialchars($s['status'] ?: '-')."</small>";
            echo "</div>";

            // ✅ Mostrar descripción de subtarea
            if (!empty($s['description_md'])) {
                echo "<div class='subtarea-descripcion'>".nl2br(htmlspecialchars($s['description_md']))."</div>";
            }

            echo "<div class='subtarea-meta'>";
            echo "<span>Prioridad:</span> ".htmlspecialchars($s['prioridad'])." | ";
            echo "<span>".htmlspecialchars($s['etiquetas'])."</span> | ";
            echo "<span>Inicio: ".($s['start_date'] ?: '-')." / Vence: ".($s['due_date'] ?: '-')."</span>";
            
            // Mostrar archivos si existen
            if (!empty($s['archivo'])) {
              echo "<div class='archivos-tarea'><span>Archivos:</span> ";
              foreach (explode(',', $s['archivo']) as $archivo) {
                $archivo = trim($archivo);
                if ($archivo) {
                  echo "<a href='uploads/".htmlspecialchars($archivo)."' target='_blank'>📎 ".htmlspecialchars($archivo)."</a> ";
                }
              }
              echo "</div>";
            }

            echo "</div>";
            echo "<div class='subtarea-actions'>";
            echo " <a href='editar.php?id={$s['id']}'><img src='svg/lucide--edit(1).svg'></a>";
            echo " <a href='comentarios.php?task_id={$s['id']}'><img src='svg/ic--twotone-message.svg'></a>";
            echo " <a href='eliminar.php?id={$s['id']}' onclick=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\"><img src='svg/material-symbols--close (1).svg'></a>";
            echo "</div>";
            listarSubtareasVista($conn, $s['id'], $nivel+1, $user_id);
            echo "</li>";
        }
        echo "</ul>";
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
        <a href="tareas.php">Agregar Tareas</a>
        <a href="mis_proyectos.php">Ver mis Proyectos</a>
        <?php if ($profile_pic !== "img/default_profile.png"): ?>
        <a href="perfil.php" class="perfil-link">
          <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Perfil" class="perfil-icon">
          <span class="perfil-nombre-navbar"><?= htmlspecialchars($user_name) ?></span>
        </a>
        <?php else: ?>
        <a href="perfil.php">Perfil</a>
        <?php endif; ?>
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
            <option value="Baja" <?= (($_GET['prioridad'] ?? '')=="Baja")?'selected':''; ?>>Baja</option>
          </select>

          <p>Estado:</p>
          <select name="estado">
            <option value="">Todos</option>
            <option value="Pendiente" <?= (($_GET['estado'] ?? '')=="Pendiente")?'selected':''; ?>>Pendiente</option>
            <option value="En_progreso" <?= (($_GET['estado'] ?? '')=="En_progreso")?'selected':''; ?>>En Progreso</option>
            <option value="Completada" <?= (($_GET['estado'] ?? '')=="Completada")?'selected':''; ?>>Completada</option>
          </select>
          <br>
          <p>Inicio:</p>
          <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">

          <p>Vence:</p>
          <input type="date" name="fecha_vencimiento" value="<?= htmlspecialchars($_GET['fecha_vencimiento'] ?? '') ?>">

          <button class="buscar" type="submit">Buscar</button>
          <button type='button' class="limpiar" onclick="window.location.href='inicio.php'">Limpiar</button>
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
                <div class="tarea-header">
                  <b><?= htmlspecialchars($t['title']) ?></b> 
                  <small><?= htmlspecialchars($t['status'] ?: ' -') ?></small>
                  <?php if ($t['tipo_tarea'] == 'proyecto'): ?>
                    <span class="badge-proyecto">| <?= htmlspecialchars($t['project_name'] ?? 'Proyecto') ?></span>
                  <?php else: ?>
                    <span class="badge-personal">| Personal</span>
                  <?php endif; ?>
                </div>

                <!-- ✅ Mostrar descripción -->
                <?php if (!empty($t['description_md'])): ?>
                  <div class="tarea-descripcion">
                    <?= nl2br(htmlspecialchars($t['description_md'])) ?>
                  </div>
                <?php endif; ?>

                <div class="tarea-meta">
                  <span>Prioridad:</span> <?= htmlspecialchars($t['prioridad']) ?> | 
                  <span><?= htmlspecialchars($t['etiquetas']) ?></span> | 
                  <span>Inicio: <?= $t['start_date'] ?: '-' ?> / Vence: <?= $t['due_date'] ?: '-' ?></span>
                  
                  <?php if (!empty($t['archivo'])): ?>
                    <div class="archivos-tarea">
                      <span>Archivos:</span>
                      <?php foreach (explode(',', $t['archivo']) as $archivo): $archivo = trim($archivo); if ($archivo): ?>
                        <a href="uploads/<?= htmlspecialchars($archivo) ?>" target="_blank">📎 <?= htmlspecialchars($archivo) ?></a>
                      <?php endif; endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="tarea-actions">
                  <?php if ($t['tipo_tarea'] == 'proyecto'): ?>
                    <!-- Acciones para tareas de proyecto -->
                    <a href="Admin/editar_tarea.php?id=<?= $t['id'] ?>&project_id=<?= $t['project_id'] ?>"><img src="svg/lucide--edit(1).svg" alt="Editar"></a>
                    <a href="comentarios.php?task_id=<?= $t['id'] ?>"><img src="svg/ic--twotone-message.svg" alt="Comentarios"></a>
                    <a href="Admin/eliminar_tarea.php?id=<?= $t['id'] ?>&project_id=<?= $t['project_id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')"><img src="svg/material-symbols--close (1).svg" alt="Eliminar"></a>
                  <?php else: ?>
                    <!-- Acciones para tareas personales -->
                    <a href="editar.php?id=<?= $t['id'] ?>"><img src="svg/lucide--edit(1).svg" alt="Editar"></a>
                    <a href="comentarios.php?task_id=<?= $t['id'] ?>"><img src="svg/ic--twotone-message.svg" alt="Comentarios"></a>
                    <a href="eliminar.php?id=<?= $t['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')"><img src="svg/material-symbols--close (1).svg" alt="Eliminar"></a>
                  <?php endif; ?>
                </div>
                <?php if ($t['tipo_tarea'] == 'normal'): ?>
                  <?php listarSubtareasVista($conn, $t['id'], 1, $user_id); ?>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
      </ul>
  </main>
</body>
</html>
