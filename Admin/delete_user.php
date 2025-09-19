<?php
session_start();
include("../config.php");

// Validar sesión
if (!isset($_SESSION["user_id"])) {
    die("Acceso denegado.");
}

// Verificar rol del admin
$yo = $conn->query("SELECT role FROM users WHERE id=".(int)$_SESSION["user_id"])->fetch_assoc();
if (!$yo || $yo["role"] !== "admin") {
    die("Acceso restringido.");
}

// Validar usuario a eliminar
if (!isset($_POST["id"])) {
    die("ID no recibido.");
}

$id = (int)$_POST["id"];

// Impedir auto-eliminación
if ($id === (int)$_SESSION["user_id"]) {
    die("No puedes eliminarte a ti mismo.");
}

// Revisar si el usuario tiene tareas creadas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE creator_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if ($data["total"] > 0) {
    // Tiene tareas → no se elimina
    header("Location: panel_admin.php?error=usuario_con_tareas");
    exit;
}

// Si no tiene tareas, borrar usuario
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Error al eliminar usuario: " . $stmt->error);
}

$stmt->close();
$conn->close();

header("Location: panel_admin.php?msg=usuario_eliminado");
exit;
?>
<!DOCTYPE html> 