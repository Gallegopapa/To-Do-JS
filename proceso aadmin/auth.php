<?php
session_start();
require_once "config.php";

//verificar login
function login($email, $password, $conexion) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['usuario'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'rol_id' => $user['rol_id']
        ];
        $_SESSION['permisos'] = obtenerPermisosUsuario($user['id'], $conexion);
        return true;
    }
    return false;
}

//obtener permisos del usuario
function obtenerPermisosUsuario($usuarioId, $conexion) {
    $sql = "SELECT p.nombre
            FROM permisos p
            INNER JOIN rol_permiso rp ON p.id = rp.permiso_id
            INNER JOIN roles r ON rp.rol_id = r.id
            INNER JOIN users u ON u.rol_id = r.id
            WHERE u.id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$usuarioId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

//verifica permisos
function tienePermiso($permiso) {
    return in_array($permiso, $_SESSION['permisos'] ?? []);
}

//redirige si no está logueado
function requiereLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit;
    }
}
?>
