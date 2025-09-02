<?php
require "config.php";

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    if ($name && $email && $pass) {
        // Validar email duplicado
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $mensaje = "⚠️ El correo ya está registrado.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?,?,?)");
            $stmt->execute([$name, $email, $hash]);

            $mensaje = "✅ Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>";
        }
    } else {
        $mensaje = "⚠️ Completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - ToDo</title>
</head>
<body>
  <h2>Registro</h2>
  <form method="POST">
    <label>Nombre:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Registrarme</button>
  </form>

  <p><?= $mensaje ?></p>
  <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>
