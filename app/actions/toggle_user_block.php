<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /Gestion_Stage/app/views/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['type'])) {
    $user_id = (int)$_POST['user_id'];
    $type = $_POST['type'];
    $table = ($type === 'etudiant') ? 'etudiants' : 'entreprises';
    
    try {
        $stmt = $pdo->prepare("UPDATE $table SET bloque = NOT bloque WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $_SESSION['success_message'] = "Le statut de blocage a été mis à jour avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Une erreur est survenue : " . $e->getMessage();
    }
}

header('Location: /Gestion_Stage/app/views/panels/admin_panel.php#utilisateurs');
exit();