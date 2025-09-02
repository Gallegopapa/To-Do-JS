<?php
// Datos de conexión
$host = "localhost";
$dbname = "to-do-js"; 
$user = "root";       
$pass = "123456";           

// Conexión
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Verificar conexión
if (!$conn) {
    die("❌ Error de conexión: " . mysqli_connect_error());
} else {
}
?>
