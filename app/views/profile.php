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

$table = ($_SESSION['role'] == 'etudiant') ? 'etudiants' : 'entreprises';
$user = get_user($pdo, $_SESSION['user_id'], $table);

if (!$user) {
    session_destroy();
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$errors = [];
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traiter la mise à jour du profil ici
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Vérification des exigences du mot de passe
    if (!empty($new_password)) {
        if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    // Si aucun mot de passe n'est fourni, ne pas modifier le mot de passe
    if (empty($errors)) {
        if (!empty($new_password)) {
            $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashed_password, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
        }

        // Rafraîchir les données de l'utilisateur
        $user = get_user($pdo, $_SESSION['user_id'], $table);

        // Message de succès
        $successMessage = "Votre profil a été mis à jour avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Mon profil</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <h1>Mon profil</h1>

        <!-- Affichage des erreurs -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Message de succès -->
        <?php if (!empty($successMessage)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="new_password">Nouveau mot de passe (laisser vide pour ne pas changer):</label>
            <input type="password" id="new_password" name="new_password">

            <?php if ($_SESSION['role'] == 'etudiant'): ?>
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
                <label for="nom_entreprise">Nom de l'entreprise:</label>
                <input type="text" id="nom_entreprise" name="nom_entreprise" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

                <label for="siret">SIRET:</label>
                <input type="text" id="siret" name="siret" value="<?php echo htmlspecialchars($user['siret']); ?>" required>
            <?php endif; ?>

            <button type="submit">Mettre à jour le profil</button>
        </form>

        <p><a class="index-button" href="/Gestion_Stage/app/views/home.php">Retour à l'espace personnel</a></p>
    </div>
</body>
</html>
