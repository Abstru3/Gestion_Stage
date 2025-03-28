<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];
    
    $result = login($pdo, $identifier, $password);

    if (is_array($result) && isset($result['error'])) {
        $error = $result['error'];
    } elseif ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['username'] = $result['username'];
        
        header('Location: /Gestion_Stage/app/views/home.php');
        exit();
    } else {
        $error = "Identifiant ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Connexion</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_login.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="post">
            <label for="identifier">Email ou nom d'utilisateur :</label>
            <input type="text" id="identifier" name="identifier" required>

            <div class="password-container">
                <label for="password">Mot de passe :</label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit">Se connecter</button>
        </form>
        <p class="link-button">Pas encore inscrit ? <a href="/Gestion_Stage/app/views/auth/register.php">S'inscrire</a></p>
    </div>

    <a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
