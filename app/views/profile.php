<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = get_user($pdo, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traiter la mise à jour du profil ici
    // ...
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <div class="container">
    <h1>Mon profil</h1>
    <form action="" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="new_password">Nouveau mot de passe (laisser vide pour ne pas changer):</label>
        <input type="password" id="new_password" name="new_password">

        <button type="submit">Mettre à jour le profil</button>
    </form>
    <p><a href="home.php">Retour à l'accueil</a></p>
</body>
</html>