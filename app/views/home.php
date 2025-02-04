<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/views/internships/post_internship.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = get_user($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
   <h1>Bienvenue, <?php echo htmlspecialchars($user['email']); ?></h1>

    <nav>
        <ul>
            <li><a href="profile.php">Mon profil</a></li>
            <?php if ($user['role'] == 'etudiant'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/student_panel.php">Offres de stages</a></li>
            <?php elseif ($user['role'] == 'entreprise'): ?>
                <li><a href="/Gestion_Stage/app/views/internships/post_internship.php">Publier une offre de stage</a></li>
                <li><a href="/Gestion_Stage/app/views/panels/company_panel.php">Gérer les candidatures</a></li>
            <?php elseif ($user['role'] == 'admin'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/admin_panel.php">Panel d'administration</a></li>
            <?php endif; ?>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</body>
</html>