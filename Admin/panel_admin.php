<?php
session_start();
include("../config.php");

//valida que sea admin
if (!isset($_SESSION["user_id"])) {
    die("Acceso denegado. No hay sesión.");
}

// buscamos al usuario logueado
$yo = $conn->query("SELECT role, name, profile_pic FROM users WHERE id=".(int)$_SESSION["user_id"])->fetch_assoc();
if (!$yo || $yo["role"] !== "admin") {
    die("Acceso restringido. Solo admins pueden entrar.");
}

// traer usuarios
$usuarios = $conn->query("SELECT id, name FROM users ORDER BY name ASC");

// si se selecciona un usuario
$usuario = null;
if (isset($_GET["usuario_id"])) {
    $uid = (int)$_GET["usuario_id"];
    $usuario = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Admin</title>
  <link rel="stylesheet" href="../css/inicio.css">  
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>        
  <header class="header">
    <div class="container">
      <div class="btn-menu"><label for="btn-menu">☰</label></div>
      <div class="logo"><a href="inico_Admin.php"><h1>GESTOR TAREAS</h1></a></div>
      <nav class="menu">
        <a href="crear_proyecto.php">Gestionar Proyectos</a>
        <a href="panel_admin.php">Panel Admin</a>
        <a href="../perfil.php" class="perfil-link">
          <img src="<?= htmlspecialchars($yo["profile_pic"]) ?>" alt="Perfil" class="perfil-icon">
          <span class="perfil-nombre-navbar"><?= htmlspecialchars($yo["name"]) ?></span>
        </a>
        <a class="cerrar_sesion" href="../logout.php">Cerrar sesión</a>
      </nav>
    </div>
  </header>

  <div class="capa"></div>
  <input type="checkbox" id="btn-menu" />
  <div class="container-menu">
    <div class="cont-menu">
      <nav>
        <h1 class="bienvenida">
          Bienvenid@ <span class="user_name"><?= htmlspecialchars($yo["name"]) ?></span> (Admin)
        </h1>
      </nav>
      <label for="btn-menu">✘</label>
    </div>
  </div>

  <main class="contenido_admin" style="margin-top:120px;">
    <h1>Panel de Administración</h1>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "usuario_con_tareas"): ?>
      <div style="background:#f8d7da; color:#721c24; padding:10px; border:1px solid #f5c6cb; border-radius:5px; margin-bottom:15px;">
        ❌ No puedes eliminar este usuario porque tiene tareas asignadas. 
        Elimina o reasigna sus tareas primero.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET["msg"]) && $_GET["msg"] === "usuario_eliminado"): ?>
      <div style="background:#d4edda; color:#155724; padding:10px; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:15px;">
        ✅ Usuario eliminado con éxito.
      </div>
    <?php endif; ?>

    <form method="get">
      <label for="usuario_id">Seleccionar Usuario:</label>
      <select name="usuario_id" id="usuario_id" onchange="this.form.submit()">
        <option value="">-- Selecciona --</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
          <option value="<?= $u['id'] ?>" <?= ($usuario && $usuario['id']==$u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <?php if ($usuario): ?>
      <div class="card">
        <div class="left">
          <h2>Gestión de <?= htmlspecialchars($usuario["name"]) ?></h2>

          <?php
            $filename = $usuario["profile_pic"];
            $avatar = (!empty($filename) && file_exists(__DIR__ . '/../img/perfiles/' . $filename))
                      ? '/img/perfiles/' . $filename
                      : '/img/avatar_default.jpg'; //imagen por defecto
          ?>
          <img src="<?= htmlspecialchars($avatar) ?>" alt="" class="perfil-icon-grande">
        </div>
        <div class="right">
          <form method="post" action="update_user.php">
            <input type="hidden" name="id" value="<?= $usuario["id"] ?>">

            <label>Nombre:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($usuario["name"]) ?>">

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario["email"]) ?>">

            <label>Rol:</label>
            <select name="role">
              <option value="user" <?= $usuario["role"]=="user" ? "selected" : "" ?>>Usuario</option>
              <option value="moderator" <?= $usuario["role"]=="moderator" ? "selected" : "" ?>>Moderador</option>
              <option value="admin" <?= $usuario["role"]=="admin" ? "selected" : "" ?>>Administrador</option>
            </select>

            <label>Estado:</label>
            <select name="is_active">
              <option value="1" <?= $usuario["is_active"]==1 ? "selected" : "" ?>>Activo</option>
              <option value="0" <?= $usuario["is_active"]==0 ? "selected" : "" ?>>Inactivo</option>
            </select>

            <button type="submit" class="btn">Guardar cambios</button>
          </form>

          <!-- Botón eliminar usuario -->
          <?php if ($usuario["id"] != $_SESSION["user_id"]): ?>
            <form method="post" action="delete_user.php" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');" style="margin-top:10px;">
              <input type="hidden" name="id" value="<?= $usuario["id"] ?>">
              <button type="submit" class="btn btn-danger">Eliminar usuario</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
