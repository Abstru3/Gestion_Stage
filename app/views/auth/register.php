<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (register($pdo, $username, $password, $email, $role)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Erreur lors de l'inscription.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <div class="container">
    <h1>Inscription</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form action="" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="role">Rôle:</label>
        <select id="role" name="role" required>
            <option value="etudiant">Étudiant</option>
            <option value="entreprise">Entreprise</option>
        </select>

        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit? <a href="login.php">Se connecter</a></p>
</body>
</html>