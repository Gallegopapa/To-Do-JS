<?php
require "config.php";

session_start();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    //esto hace al consulta para obtener el usuario con ese correo
    $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    //esto verifica si se encontró el usuario
    if ($user = $result->fetch_assoc()) {
        if (password_verify($pass, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            header("Location: inicio.php");
            exit;
        } else {
            $mensaje = "Contraseña incorrecta";
        }
    } else {
        $mensaje = "El correo no está registrado";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/login.css">
  <title>Login - ToDo</title>
</head>
<body>
  <h2>Iniciar Sesión</h2>
  <form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>

    <p><a href="forgot_password.php">¿Has olvidado tu contraseña?</a></p>

  </form>

  <p style="color:red;"><?= $mensaje ?></p>
  <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</body>
</html>
