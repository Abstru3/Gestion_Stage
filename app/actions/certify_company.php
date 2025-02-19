<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /Gestion_Stage/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entreprise_id'])) {
    $entreprise_id = (int)$_POST['entreprise_id'];
    
    $stmt = $pdo->prepare("UPDATE entreprises SET certification = 1 WHERE id = ?");
    if ($stmt->execute([$entreprise_id])) {
        $_SESSION['success_message'] = "L'entreprise a été certifiée avec succès.";
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la certification.";
    }
}

header('Location: /Gestion_Stage/app/views/panels/admin_panel.php');
exit();