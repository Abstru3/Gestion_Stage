<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT cv FROM etudiants WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();

if (empty($student['cv'])) {
    $_SESSION['error'] = "Vous devez d'abord ajouter votre CV dans votre profil pour postuler.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['offre_id'])) {
    $offre_id = (int)$_POST['offre_id'];
    
    $etudiant_cv = $student['cv'];
    
    $stmt = $pdo->prepare("SELECT id FROM candidatures WHERE etudiant_id = ? AND offre_id = ?");
    $stmt->execute([$_SESSION['user_id'], $offre_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Vous avez déjà postulé à cette offre.";
        header('Location: /Gestion_Stage/app/views/internships/stage_details.php?id=' . $offre_id);
        exit();
    }

    if (isset($_FILES['lettre_motivation']) && $_FILES['lettre_motivation']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/public/uploads/candidatures/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['lettre_motivation']['name']);
        if (strtolower($fileInfo['extension']) === 'pdf') {
            $newFileName = uniqid() . '_lettre_' . $_SESSION['user_id'] . '.pdf';
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['lettre_motivation']['tmp_name'], $uploadFile)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO candidatures (etudiant_id, offre_id, cv, lettre_motivation, date_candidature, statut) 
                                          VALUES (?, ?, ?, ?, NOW(), 'en_attente')");
                    
                    error_log("Tentative d'insertion - etudiant_id: " . $_SESSION['user_id'] . 
                              ", offre_id: " . $offre_id . 
                              ", cv: " . $etudiant_cv . 
                              ", lettre: " . $newFileName);
                    
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $offre_id,
                        $etudiant_cv,
                        $newFileName
                    ]);

                    $_SESSION['success'] = "Votre candidature a été envoyée avec succès !";
                    header('Location: /Gestion_Stage/app/views/panels/student_panel.php');
                    exit();
                } catch (PDOException $e) {
                    error_log("Erreur SQL: " . $e->getMessage());
                    $_SESSION['error'] = "Une erreur est survenue lors de l'envoi de votre candidature: " . $e->getMessage();
                    unlink($uploadFile);
                }
            } else {
                $_SESSION['error'] = "Erreur lors du téléchargement de la lettre de motivation.";
            }
        } else {
            $_SESSION['error'] = "Format de fichier non autorisé pour la lettre de motivation. Utilisez uniquement le format PDF.";
        }
    } else {
        $_SESSION['error'] = "Veuillez fournir une lettre de motivation.";
    }

    header('Location: /Gestion_Stage/app/views/internships/stage_details.php?id=' . $offre_id);
    exit();
}

header('Location: /Gestion_Stage/index.php');
exit();
?>
