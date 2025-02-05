<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Définition des constantes pour les messages d'erreur
define('ERROR_REQUIRED_FIELDS', "Tous les champs doivent être remplis.");
define('ERROR_INVALID_EMAIL', "Adresse email invalide.");
define('ERROR_EMAIL_EXISTS', "Cet email est déjà utilisé.");
define('ERROR_PASSWORD_REQUIREMENTS', "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.");
define('ERROR_PASSWORD_MISMATCH', "Les mots de passe ne correspondent pas.");
define('ERROR_INVALID_SIRET', "Le numéro SIRET doit contenir exactement 14 chiffres.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $honeypot = filter_input(INPUT_POST, 'honeypot', FILTER_SANITIZE_STRING);
    $errors = [];

    // Vérification du champ honeypot (anti-robots)
    if (!empty($honeypot)) {
        die("Inscription bloquée.");
    }

    // Vérification des champs obligatoires
    if (empty($username) || empty($password) || empty($email) || empty($role)) {
        $errors[] = ERROR_REQUIRED_FIELDS;
    }

    // Vérification de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = ERROR_INVALID_EMAIL;
    }

    // Vérification de l'unicité de l'email
    $stmt = $pdo->prepare("SELECT id FROM " . ($role === 'etudiant' ? 'etudiants' : 'entreprises') . " WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = ERROR_EMAIL_EXISTS;
    }

    // Vérification du mot de passe
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = ERROR_PASSWORD_REQUIREMENTS;
    }

    // Vérification de la confirmation du mot de passe
    if ($password !== $confirm_password) {
        $errors[] = ERROR_PASSWORD_MISMATCH;
    }

    // Si aucune erreur, on enregistre dans la base
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            if ($role === 'etudiant') {
                $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
                $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);

                if (empty($nom) || empty($prenom)) {
                    throw new Exception(ERROR_REQUIRED_FIELDS);
                }

                $stmt = $pdo->prepare("INSERT INTO etudiants (username, email, password, nom, prenom) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $nom, $prenom]);
            } elseif ($role === 'entreprise') {
                $nom_entreprise = filter_input(INPUT_POST, 'nom_entreprise', FILTER_SANITIZE_STRING);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $adresse_facturation = filter_input(INPUT_POST, 'adresse_facturation', FILTER_SANITIZE_STRING);
                $nom_contact = filter_input(INPUT_POST, 'nom_contact', FILTER_SANITIZE_STRING);
                $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
                $site_web = filter_input(INPUT_POST, 'site_web', FILTER_SANITIZE_URL);
                $tva_intracommunautaire = filter_input(INPUT_POST, 'tva_intracommunautaire', FILTER_SANITIZE_STRING);
                $siret = filter_input(INPUT_POST, 'siret', FILTER_SANITIZE_STRING);

                if (empty($nom_entreprise) || empty($description) || empty($adresse_facturation) || empty($siret)) {
                    throw new Exception(ERROR_REQUIRED_FIELDS);
                }

                // Vérification du format SIRET
                if (!preg_match('/^[0-9]{14}$/', $siret)) {
                    throw new Exception(ERROR_INVALID_SIRET);
                }

                $stmt = $pdo->prepare("INSERT INTO entreprises (username, email, password, nom, description, adresse_facturation, nom_contact, telephone, site_web, tva_intracommunautaire, siret) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $nom_entreprise, $description, $adresse_facturation, $nom_contact, $telephone, $site_web, $tva_intracommunautaire, $siret]);
            }

            $pdo->commit();
            $_SESSION['success'] = "Inscription réussie ! Connectez-vous.";
            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion des Stages</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_register.css">
    <script>
        function updateForm() {
            const role = document.getElementById("role").value;
            const formContent = document.getElementById("dynamic-form-content");

            // Récupérer les valeurs précédentes avec PHP
            const email = `<?= htmlspecialchars($_POST['email'] ?? '') ?>`;
            const username = `<?= htmlspecialchars($_POST['username'] ?? '') ?>`;
            const nom = `<?= htmlspecialchars($_POST['nom'] ?? '') ?>`;
            const prenom = `<?= htmlspecialchars($_POST['prenom'] ?? '') ?>`;
            const nom_entreprise = `<?= htmlspecialchars($_POST['nom_entreprise'] ?? '') ?>`;
            const description = `<?= htmlspecialchars($_POST['description'] ?? '') ?>`;
            const adresse_facturation = `<?= htmlspecialchars($_POST['adresse_facturation'] ?? '') ?>`;
            const nom_contact = `<?= htmlspecialchars($_POST['nom_contact'] ?? '') ?>`;
            const telephone = `<?= htmlspecialchars($_POST['telephone'] ?? '') ?>`;
            const site_web = `<?= htmlspecialchars($_POST['site_web'] ?? '') ?>`;

            // Champs communs
            const commonFields = `
                <div class="form-section">
                    <h2>Informations de connexion</h2>
                    <div class="form-group">
                        <label for="email">Email professionnel :</label>
                        <input type="email" id="email" name="email" required value="${email}"
                               placeholder="votre.email@exemple.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur :</label>
                        <input type="text" id="username" name="username" required value="${username}"
                               placeholder="Choisissez un nom d'utilisateur">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="8 caractères minimum">
                        <small>Minimum 8 caractères, une majuscule et un chiffre</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmation du mot de passe :</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               required placeholder="Confirmez votre mot de passe">
                    </div>
                </div>
            `;

            let specificFields = '';

            if (role === "etudiant") {
                specificFields = `
                    <div class="form-section">
                        <h2>Informations personnelles</h2>
                        <div class="form-group">
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" required value="${nom}"
                                   placeholder="Votre nom de famille">
                        </div>

                        <div class="form-group">
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" required value="${prenom}"
                                   placeholder="Votre prénom">
                        </div>
                    </div>
                `;
            } else if (role === "entreprise") {
                specificFields = `
                    <div class="form-section">
                        <h2>Informations de l'entreprise</h2>
                        <div class="form-group">
                            <label for="nom_entreprise">Raison sociale :</label>
                            <input type="text" id="nom_entreprise" name="nom_entreprise" required value="${nom_entreprise}"
                                   placeholder="Nom de votre entreprise">
                        </div>

                        <div class="form-group">
                            <label for="description">Présentation de l'entreprise :</label>
                            <textarea id="description" name="description" required 
                                    placeholder="Décrivez brièvement votre entreprise">${description}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="adresse_facturation">Adresse de facturation :</label>
                            <input type="text" id="adresse_facturation" name="adresse_facturation" 
                                   required value="${adresse_facturation}" placeholder="Adresse complète">
                        </div>

                        <div class="form-group">
                            <label for="nom_contact">Personne à contacter :</label>
                            <input type="text" id="nom_contact" name="nom_contact" 
                                   value="${nom_contact}" placeholder="Nom et prénom">
                        </div>

                        <div class="form-group">
                            <label for="siret">SIRET :</label>
                            <input type="text" id="siret" name="siret" required 
                                   value="<?= htmlspecialchars($_POST['siret'] ?? '') ?>" 
                                   placeholder="Numéro SIRET (14 chiffres)">
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone :</label>
                            <input type="tel" id="telephone" name="telephone" 
                                   value="${telephone}" placeholder="01 23 45 67 89">
                        </div>

                        <div class="form-group">
                            <label for="site_web">Site Web :</label>
                            <input type="url" id="site_web" name="site_web" 
                                   value="${site_web}" placeholder="https://www.exemple.com">
                        </div>
                    </div>
                `;
            }

            formContent.innerHTML = commonFields + specificFields;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Créer un compte</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-section">
                <div class="form-group">
                    <label for="role">Je suis :</label>
                    <select id="role" name="role" required onchange="updateForm()">
                        <option value="etudiant" <?= (isset($_POST['role']) && $_POST['role'] == "etudiant") ? "selected" : "" ?>>Un étudiant</option>
                        <option value="entreprise" <?= (isset($_POST['role']) && $_POST['role'] == "entreprise") ? "selected" : "" ?>>Une entreprise</option>
                    </select>
                </div>
            </div>

            <div id="dynamic-form-content"></div>
            
            <input type="hidden" name="honeypot">
            <button type="submit">Créer mon compte</button>
        </form>

        <p class="link-button">
            Déjà inscrit ? <a href="login.php">Se connecter</a>
        </p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateForm();
        });
    </script>
</body>
</html>
