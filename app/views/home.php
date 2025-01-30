<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?></h1>
    <nav>
        <ul>
            <li><a href="profile.php">Mon profil</a></li>
            <?php if ($user['role'] == 'etudiant'): ?>
                <li><a href="student_panel.php">Offres de stages</a></li>
            <?php elseif ($user['role'] == 'entreprise'): ?>
                <li><a href="post_internship.php">Publier une offre de stage</a></li>
                <li><a href="company_panel.php">Gérer les candidatures</a></li>
            <?php elseif ($user['role'] == 'admin'): ?>
                <li><a href="admin_panel.php">Panel d'administration</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</body>
</html>