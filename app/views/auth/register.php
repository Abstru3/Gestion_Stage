<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = $_POST['role'];
    $honeypot = $_POST['honeypot'] ?? ''; 
    $errors = [];

    // Vérification du champ honeypot (anti-robots)
    if (!empty($honeypot)) {
        die("Inscription bloquée.");
    }

    // Vérifier que les champs obligatoires sont remplis
    if (empty($username) || empty($password) || empty($email) || empty($role)) {
        $errors[] = "Tous les champs doivent être remplis.";
    }

    // Vérifier si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Cet email est déjà utilisé.";
    }

    // Vérifier la complexité du mot de passe
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
    }

    // Vérifier la confirmation du mot de passe
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Si aucune erreur, on enregistre dans la base
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($role === 'etudiant') {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);

            if (empty($nom) || empty($prenom)) {
                $errors[] = "Tous les champs pour l'étudiant doivent être remplis.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO etudiants (username, email, password, nom, prenom) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $nom, $prenom]);
            }
        } elseif ($role === 'entreprise') {
            $nom_entreprise = trim($_POST['nom_entreprise']);
            $description = trim($_POST['description']);
            $adresse_facturation = trim($_POST['adresse_facturation']);
            $nom_contact = trim($_POST['nom_contact']);
            $telephone = $_POST['telephone'];
            $site_web = $_POST['site_web'];
            $tva_intracommunautaire = $_POST['tva_intracommunautaire'];

            if (empty($nom_entreprise) || empty($description) || empty($adresse_facturation) || empty($telephone)) {
                $errors[] = "Tous les champs pour l'entreprise doivent être remplis.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO entreprises (username, email, password, nom_entreprise, description, adresse_facturation, nom_contact, telephone, site_web, tva_intracommunautaire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $nom_entreprise, $description, $adresse_facturation, $nom_contact, $telephone, $site_web, $tva_intracommunautaire]);
            }
        }

        $_SESSION['success'] = "Inscription réussie ! Connectez-vous.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_register.css">
    <script>
        function updateForm() {
            var role = document.getElementById("role").value;
            var extraInfoField = document.getElementById("extra-info-field");

            let passwordFields = `
                <fieldset>
                    <legend>Sécurité</legend>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required placeholder="Min. 8 caractères, 1 majuscule, 1 chiffre">
                    <small>Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.</small>

                    <label for="confirm_password">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirmez votre mot de passe">
                </fieldset>
            `;

            if (role === "etudiant") {
                extraInfoField.innerHTML = `
                    <fieldset>
                        <legend>Informations personnelles</legend>
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required placeholder="Ex : Dupont">

                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required placeholder="Ex : Jean">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required placeholder="Ex : jean.dupont@email.com">


                    </fieldset>

                    ${passwordFields}
                `;
            } else if (role === "entreprise") {
                extraInfoField.innerHTML = `
                    <fieldset>
                        <legend>Informations sur l'entreprise</legend>
                        <label for="nom_entreprise">Nom de l'entreprise :</label>
                        <input type="text" id="nom_entreprise" name="nom_entreprise" required placeholder="Ex : TechCorp">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required placeholder="Ex : jean.dupont@email.com">


                    ${passwordFields}

                        <label for="description">Description :</label>
                        <textarea id="description" name="description" required placeholder="Décrivez brièvement votre activité"></textarea>

                        <label for="adresse_facturation">Adresse de facturation :</label>
                        <input type="text" id="adresse_facturation" name="adresse_facturation" required placeholder="Ex : 10 rue des affaires, Paris">

                        <label for="nom_contact">Nom du contact :</label>
                        <input type="text" id="nom_contact" name="nom_contact" required placeholder="Ex : Pierre Martin">

                        <label for="telephone">Téléphone :</label>
                        <input type="text" id="telephone" name="telephone" required placeholder="Ex : 06 12 34 56 78">

                        <label for="site_web">Site Web :</label>
                        <input type="url" id="site_web" name="site_web" placeholder="Ex : https://www.mon-entreprise.com">

                        <label for="tva_intracommunautaire">TVA intracommunautaire :</label>
                        <input type="text" id="tva_intracommunautaire" name="tva_intracommunautaire" placeholder="Ex : FR123456789">
                    </fieldset>
                `;
            } else {
                extraInfoField.innerHTML = "";
            }
        }
    </script>
</head>
<body onload="updateForm()">
    <div class="container">
        <h1>Inscription</h1>
        <form action="" method="post">
            <fieldset>
                <legend>Informations générales</legend>
                <label for="role">Rôle :</label>
                <select id="role" name="role" required onchange="updateForm()">
                    <option value="etudiant">Étudiant</option>
                    <option value="entreprise">Entreprise</option>
                </select>
            </fieldset>
            <div id="extra-info-field"></div>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
    <p class="link-button">Déjà inscrit? <a href="login.php">Se connecter</a></p>
</body>
</html>
