<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Utilisation de l'email au lieu de username
    $password = $_POST['password'];

    $user = login($pdo, $email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($_SESSION['role'] == 'admin') {
            header("Location: /Gestion_Stage/app/views/panels/admin_panel.php");
        } else {
            header("Location: /Gestion_Stage/app/views/home.php");
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_login.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="post">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <p class="link-button">Pas encore inscrit ? <a href="/Gestion_Stage/app/views/auth/register.php">S'inscrire</a></p>
    </div>

    <a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a>
</body>
</html>
