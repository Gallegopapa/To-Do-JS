<?php
$host = "localhost";
$dbname = "to-do-js"; 
$user = "root";       
$pass = "123456";           

$conn = mysqli_connect($host, $user, $pass, $dbname);


if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
} else {
}
?>
