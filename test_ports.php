<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'smtp.gmail.com';
$ports = [587, 465];
foreach ($ports as $port) {
    $fp = @fsockopen($host, $port, $errno, $errstr, 10);
    if ($fp) {
        echo "Conexión a {$host}:{$port} → <b>OK</b><br>";
        fclose($fp);
    } else {
        echo "Conexión a {$host}:{$port} → <b style='color:red'>FALLÓ</b> — {$errstr} ({$errno})<br>";
    }
}
