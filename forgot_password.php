<?php
require "config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // === Validación simple de email ===
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ Ingresa un correo válido.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $token = bin2hex(random_bytes(50));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expiration=? WHERE id=?");
            $stmt->bind_param("ssi", $token, $expira, $user["id"]);
            $stmt->execute();

            $link = "http://localhost/To-Do-JS/reset_password.php?token=$token";

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

                $mail->Username = 'simon.23051997@gmail.com';
                $mail->Password = 'lnjquazbfeasiufv';

                // Conexión TLS
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->CharSet = "UTF-8";

                $mail->setFrom('simon.23051997@gmail.com', 'Gestor de Tareas');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Recupera tu contraseña';
                $mail->Body    = "Haz clic aquí para restablecer tu contraseña: 
                    <br><br><a href='$link'>$link</a>
                    <br><br>Este enlace expira en 1 hora.";

                $mail->AltBody = "Haz clic en este enlace para restablecer tu contraseña: $link";

                if ($mail->send()) {
                    $mensaje = "✅ Se ha enviado un enlace a tu correo.";
                } else {
                    $mensaje = "❌ Error inesperado al enviar el correo.";
                }

            } catch (Exception $e) {
                $mensaje = "❌ Error al enviar: {$mail->ErrorInfo}";
            }
        } else {
            $mensaje = "⚠️ El correo ingresado no está registrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/forgot_password.css">
  <title>Recuperar contraseña</title>
</head>
<body>
  <div class="card">
    <h2>Recuperar contraseña</h2>
    <form method="POST">
      <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <button type="submit">Enviar enlace</button>
    </form>
    <p><?= $mensaje ?></p>
  </div>
</body>
</html>
