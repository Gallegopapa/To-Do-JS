<?php
require "config.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // buscar usuario
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // generar token
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // guardar token en la DB
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expiration=? WHERE id=?");
        $stmt->bind_param("ssi", $token, $expira, $user["id"]);
        $stmt->execute();

        // enviar correo con link (aquí puedes usar PHPMailer)
        $link = "http://localhost/reset_password.php?token=$token";
        mail($email, "Recupera tu contraseña", "Haz clic aquí para restablecer tu contraseña: $link");

        $mensaje = "Se ha enviado un enlace a tu correo.";
    } else {
        $mensaje = "No se encontró ese correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
</head>
<body>
  <h2>Recuperar contraseña</h2>
  <form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Enviar enlace</button>
  </form>
  <p style="color:green;"><?= $mensaje ?></p>
</body>
</html>
