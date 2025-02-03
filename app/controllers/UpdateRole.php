<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $role_id = $_POST['role_id'];

    $sql = "UPDATE users SET role_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$role_id, $user_id]);

    header("Location: /Gestion_Stage/app/views/panels/admin_panel.php");
    exit();
}
?>
