<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Debug para ver en pantalla qué pasa
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // ⚠️ Usa aquí tu Gmail y la contraseña de aplicación (16 caracteres sin espacios)
    $mail->Username   = 'simon.23051997@gmail.com';
    $mail->Password   = 'lnjquazbfeasiufv'; 

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->CharSet = "UTF-8";

    $mail->setFrom('simon.23051997@gmail.com', 'Prueba PHPMailer');
    $mail->addAddress('promhansa@gmail.com'); // pon un correo tuyo alterno

    $mail->isHTML(true);
    $mail->Subject = 'Prueba de envío PHPMailer';
    $mail->Body    = '<h3>Este es un correo de prueba</h3><p>Si lo ves, todo funciona 🚀</p>';
    $mail->AltBody = 'Este es un correo de prueba (texto plano)';

    $mail->send();
    echo "✅ Correo enviado correctamente";
} catch (Exception $e) {
    echo "❌ Error: {$mail->ErrorInfo}";
}
