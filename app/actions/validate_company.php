<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /Gestion_Stage/app/views/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entreprise_id'])) {
    $stmt = $pdo->prepare("UPDATE entreprises SET valide = TRUE WHERE id = ?");
    $stmt->execute([$_POST['entreprise_id']]);
    
    header('Location: /Gestion_Stage/app/views/panels/admin_panel.php');
    exit();
}