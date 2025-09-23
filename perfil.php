<?php
session_start();
include("config.php");

//redirige al login si no hay sesion
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

$sql = "SELECT profile_pic FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$profile_pic = "img/default_profile.png";

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['profile_pic'])) {
        $profile_pic = $row['profile_pic'];
    }
}

// Procesa la subida de la imagen
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["nueva_imagen"])) {
    $archivo = $_FILES["nueva_imagen"];
    $directorioDestino = "img/perfiles/";
    $nombreArchivo = basename($archivo["name"]);
    $rutaDestino = $directorioDestino . time() . "_" . $nombreArchivo;
    $tipoArchivo = strtolower(pathinfo($rutaDestino, PATHINFO_EXTENSION));

    // Validaciones
    $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($tipoArchivo, $tiposPermitidos) && getimagesize($archivo["tmp_name"])) {
        if (move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
            // Guardar en la base de datos
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $rutaDestino, $user_id);
            $stmt->execute();
            $stmt->close();

            // Actualizar variable de sesión si la usas
            $profile_pic = $rutaDestino;
            $mensaje = "Imagen cambiada correctamente.";
        } else {
            $mensaje = "No se pudo subir la imagen.";
        }
    } else {
        $mensaje = "Archivo invalido, solo se permiten imagenes JPG, PNG, GIF.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/perfil.css">

</head>
<body>
    <div class="perfil-container">
        <h2>Perfil de <?= htmlspecialchars($user_name) ?></h2>
        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Foto de perfil">
        
        <form method="POST" enctype="multipart/form-data">
            <p>Cambiar imagen de perfil:</p>
            <input type="file" name="nueva_imagen" accept="image/*" required><br>
            <button type="submit">Subir</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <a href="<?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'admin/inico_Admin.php' : 'inicio.php' ?>" class="btn-volver">Volver a Inicio</a>

    </div>
</body>
</html>
