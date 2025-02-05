<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Update this line to use the 'etudiants' table instead of a generic 'users' table
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];  // L'ID de l'étudiant connecté
$offre_id = $_POST['offre_id'];

// Vérifier si l'étudiant existe dans la base de données
$stmt = $pdo->prepare("SELECT id FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    echo "L'étudiant n'existe pas dans la base de données.";
    exit();
}

// Vérifier si l'étudiant a déjà postulé à cette offre
$stmt = $pdo->prepare("SELECT * FROM candidatures WHERE etudiant_id = ? AND offre_id = ?");
$stmt->execute([$etudiant_id, $offre_id]);
if ($stmt->rowCount() > 0) {
    $_SESSION['error'] = "Vous avez déjà postulé à cette offre de stage.";
} else {
    $stmt = $pdo->prepare("INSERT INTO candidatures (etudiant_id, offre_id, statut) VALUES (?, ?, 'en_attente')");
    if ($stmt->execute([$etudiant_id, $offre_id])) {
        $_SESSION['success'] = "Votre candidature a été enregistrée avec succès.";
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de l'enregistrement de votre candidature.";
        echo "<pre>" . print_r($stmt->errorInfo(), true) . "</pre>";
    }
}

header("Location: /Gestion_Stage/app/views/panels/student_panel.php");
exit();
?>

