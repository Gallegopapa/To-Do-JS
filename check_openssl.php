<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>OpenSSL</h3>";
echo "PHP version: " . PHP_VERSION . "<br>";
if (extension_loaded('openssl')) {
    echo "<b>OpenSSL está habilitado ✅</b>";
} else {
    echo "<b style='color:red'>OpenSSL NO está habilitado ❌</b><br>";
    echo "Abre php.ini (busca la línea ;extension=openssl) y quita el ;, luego reinicia Apache.";
}
