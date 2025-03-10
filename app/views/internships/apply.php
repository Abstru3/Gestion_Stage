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

$etudiant_id = $_SESSION['user_id'];
$offre_id = $_POST['offre_id'];

$stmt = $pdo->prepare("SELECT id FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();
if (!$etudiant) {
    echo "L'étudiant n'existe pas.";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM candidatures WHERE etudiant_id = ? AND offre_id = ?");
$stmt->execute([$etudiant_id, $offre_id]);
if ($stmt->rowCount() > 0) {
    $_SESSION['error'] = "Vous avez déjà postulé.";
    header("Location: /Gestion_Stage/app/views/panels/student_panel.php");
    exit();
}

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/Gestion_Stage/public/uploads/candidatures/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$allowed_types = ['pdf' => 'application/pdf'];

$cv_file = $_FILES['cv'] ?? null;
$motivation_file = $_FILES['lettre_motivation'] ?? null;
$cv_name = null;
$motivation_name = null;

function upload_file($file, $upload_dir, $allowed_types) {
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!array_key_exists($file_ext, $allowed_types) || mime_content_type($file['tmp_name']) !== $allowed_types[$file_ext]) {
            return null;
        }
        $file_name = uniqid() . '.' . $file_ext;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
            return $file_name;
        }
    }
    return null;
}

$cv_name = upload_file($cv_file, $upload_dir, $allowed_types);
$motivation_name = upload_file($motivation_file, $upload_dir, $allowed_types);

if (!$cv_name || !$motivation_name) {
    $_SESSION['error'] = "Erreur lors du téléchargement des fichiers.";
    header("Location: /Gestion_Stage/app/views/panels/student_panel.php");
    exit();
}

$stmt = $pdo->prepare("INSERT INTO candidatures (etudiant_id, offre_id, statut, cv, lettre_motivation) VALUES (?, ?, 'en_attente', ?, ?)");
if ($stmt->execute([$etudiant_id, $offre_id, $cv_name, $motivation_name])) {
    $_SESSION['success'] = "Votre candidature a été envoyée avec succès.";
} else {
    $_SESSION['error'] = "Erreur lors de l'envoi.";
}

header("Location: /Gestion_Stage/app/views/panels/student_panel.php");
exit();
?>
