<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $offre_id = $_POST['offre_id'];
    $etudiant_id = $_SESSION['user_id'];

    // Vérifier si l'étudiant a déjà postulé à cette offre
    $stmt = $pdo->prepare("SELECT * FROM candidatures WHERE etudiant_id = ? AND offre_id = ?");
    $stmt->execute([$etudiant_id, $offre_id]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Vous avez déjà postulé à cette offre de stage.";
    } else {
        // Insérer la nouvelle candidature
        $stmt = $pdo->prepare("INSERT INTO candidatures (etudiant_id, offre_id, statut) VALUES (?, ?, 'en_attente')");
        if ($stmt->execute([$etudiant_id, $offre_id])) {
            $_SESSION['success'] = "Votre candidature a été enregistrée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'enregistrement de votre candidature.";
        }
    }
}

header("Location: student_panel.php");
exit();
?>