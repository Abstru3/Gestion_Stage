<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

// Récupérer les statistiques et les données pour l'administration
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn();
$total_applications = $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <h1>Panneau d'administration</h1>
    <h2>Statistiques</h2>
    <ul>
        <li>Nombre total d'utilisateurs: <?php echo $total_users; ?></li>
        <li>Nombre total d'offres de stages: <?php echo $total_internships; ?></li>
        <li>Nombre total de candidatures: <?php echo $total_applications; ?></li>
    </ul>
    
    <!-- Ajouter d'autres fonctionnalités d'administration ici -->

    <p><a href="/Gestion_Stage/app/views/home.php">Retour à l'accueil</a></p>
</body>
</html>