<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

// Modifier cette ligne pour utiliser la bonne table en fonction du rôle
$user = ($_SESSION['role'] == 'etudiant') ? 
    get_user($pdo, $_SESSION['user_id'], 'etudiants') : 
    get_user($pdo, $_SESSION['user_id'], 'entreprises');

if (!$user) {
    // Si l'utilisateur n'est pas trouvé, déconnectez-le et redirigez-le vers la page de connexion
    session_destroy();
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}
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
   <h1>Bienvenue, <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></h1>

    <nav>
        <ul>
            <li><a href="profile.php">Mon profil</a></li>
            <?php if ($_SESSION['role'] == 'etudiant'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/student_panel.php">Offres de stages</a></li>
            <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
                <li><a href="/Gestion_Stage/app/views/internships/post_internship.php">Publier une offre de stage</a></li>
                <li><a href="/Gestion_Stage/app/views/panels/company_panel.php">Gérer les candidatures</a></li>
            <?php elseif ($_SESSION['role'] == 'admin'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/admin_panel.php">Panel d'administration</a></li>
            <?php endif; ?>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a>
</body>
</html>