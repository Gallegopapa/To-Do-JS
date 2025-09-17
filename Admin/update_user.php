<?php
include("../config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $is_active = (int)$_POST["is_active"];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, is_active=? WHERE id=?");
    $stmt->bind_param("sssii", $name, $email, $role, $is_active, $id);
    $stmt->execute();

    header("Location: panel_admin.php?usuario_id=".$id);
    exit;
}
?>
