<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

define('ERROR_REQUIRED_FIELDS', "Tous les champs obligatoires doivent être remplis.");
define('ERROR_INVALID_EMAIL', "Adresse email invalide.");
define('ERROR_EMAIL_EXISTS', "Cet email est déjà utilisé.");
define('ERROR_USERNAME_EXISTS', "Ce nom d'utilisateur est déjà utilisé.");
define('ERROR_PASSWORD_REQUIREMENTS', "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.");
define('ERROR_PASSWORD_MISMATCH', "Les mots de passe ne correspondent pas.");
define('ERROR_INVALID_SIRET', "Le numéro SIRET doit contenir exactement 14 chiffres.");
define('ERROR_INVALID_PHONE', "Le format du numéro de téléphone est invalide.");
define('ERROR_INVALID_URL', "L'URL du site web n'est pas valide.");
define('ERROR_FIELD_TOO_LONG', "Le champ %s ne doit pas dépasser %d caractères.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = trim(htmlspecialchars($_POST['role'] ?? ''));
    $honeypot = $_POST['honeypot'] ?? '';
    $errors = [];

    if (!empty($honeypot)) {
        die("Inscription bloquée.");
    }

    if (empty($username) || empty($password) || empty($email) || empty($role)) {
        $errors[] = ERROR_REQUIRED_FIELDS;
    }

    $maxLengths = [
        'username' => 100,
        'email' => 255,
        'nom' => 50,
        'prenom' => 50,
        'nom_entreprise' => 100,
        'description' => 1000,
        'adresse_facturation' => 255,
        'nom_contact' => 100,
        'telephone' => 20,
        'site_web' => 255
    ];

    foreach ($maxLengths as $field => $maxLength) {
        if (!empty($_POST[$field]) && strlen($_POST[$field]) > $maxLength) {
            $errors[] = sprintf(ERROR_FIELD_TOO_LONG, $field, $maxLength);
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = ERROR_INVALID_EMAIL;
    }

    $stmt_email = $pdo->prepare("SELECT 'etudiant' as type FROM etudiants WHERE email = ? 
                                UNION SELECT 'entreprise' as type FROM entreprises WHERE email = ?");
    $stmt_email->execute([$email, $email]);
    
    $stmt_username = $pdo->prepare("SELECT 'etudiant' as type FROM etudiants WHERE username = ? 
                                   UNION SELECT 'entreprise' as type FROM entreprises WHERE username = ?");
    $stmt_username->execute([$username, $username]);

    if ($stmt_email->fetch()) {
        $errors[] = ERROR_EMAIL_EXISTS;
    }
    if ($stmt_username->fetch()) {
        $errors[] = ERROR_USERNAME_EXISTS;
    }

    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = ERROR_PASSWORD_REQUIREMENTS;
    }

    if ($password !== $confirm_password) {
        $errors[] = ERROR_PASSWORD_MISMATCH;
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            if ($role === 'etudiant') {
                $nom = trim(htmlspecialchars($_POST['nom'] ?? ''));
                $prenom = trim(htmlspecialchars($_POST['prenom'] ?? ''));

                if (empty($nom) || empty($prenom)) {
                    throw new Exception(ERROR_REQUIRED_FIELDS);
                }

                $stmt = $pdo->prepare("INSERT INTO etudiants (username, email, password, nom, prenom) 
                                     VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $nom, $prenom]);

                $pdo->commit();
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("Location: login.php");
                exit();

            } elseif ($role === 'entreprise') {
                $nom_entreprise = trim(htmlspecialchars($_POST['nom_entreprise'] ?? ''));
                $description = trim(htmlspecialchars($_POST['description'] ?? ''));
                $adresse_facturation = trim(htmlspecialchars($_POST['adresse_facturation'] ?? ''));
                $nom_contact = trim(htmlspecialchars($_POST['nom_contact'] ?? ''));
                $telephone = trim(htmlspecialchars($_POST['telephone'] ?? ''));
                $site_web = filter_input(INPUT_POST, 'site_web', FILTER_SANITIZE_URL);
                $tva_intracommunautaire = trim(htmlspecialchars($_POST['tva_intracommunautaire'] ?? ''));
                $siret = trim(htmlspecialchars($_POST['siret'] ?? ''));

                if (empty($nom_entreprise) || empty($description) || empty($adresse_facturation) || empty($siret)) {
                    throw new Exception(ERROR_REQUIRED_FIELDS);
                }

                if (!preg_match('/^[0-9]{14}$/', $siret)) {
                    throw new Exception(ERROR_INVALID_SIRET);
                }

                if (!empty($telephone) && !preg_match("/^[0-9]{10}$/", str_replace(' ', '', $telephone))) {
                    throw new Exception(ERROR_INVALID_PHONE);
                }

                if (!empty($site_web) && !filter_var($site_web, FILTER_VALIDATE_URL)) {
                    throw new Exception(ERROR_INVALID_URL);
                }

                $stmt = $pdo->prepare("INSERT INTO entreprises (username, email, password, nom, description, 
                                     adresse_facturation, nom_contact, telephone, site_web, 
                                     tva_intracommunautaire, siret) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $username, $email, $hashed_password, $nom_entreprise, $description,
                    $adresse_facturation, $nom_contact, $telephone, $site_web,
                    $tva_intracommunautaire, $siret
                ]);

                $pdo->commit();
                $_SESSION['info_message'] = "Votre compte entreprise a été créé avec succès ! Il est actuellement en attente de validation par un administrateur. Vous recevrez une notification par email une fois votre compte validé.";
                header("Location: /Gestion_Stage/index.php");
                exit();
            }

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
    <title>NeversStage - Inscription</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_register.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        <form action="" method="post" novalidate>
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
        function updateForm() {
            const role = document.getElementById("role").value;
            const formContent = document.getElementById("dynamic-form-content");

            const previousValues = {
                email: <?= json_encode(htmlspecialchars($_POST['email'] ?? '')) ?>,
                username: <?= json_encode(htmlspecialchars($_POST['username'] ?? '')) ?>,
                nom: <?= json_encode(htmlspecialchars($_POST['nom'] ?? '')) ?>,
                prenom: <?= json_encode(htmlspecialchars($_POST['prenom'] ?? '')) ?>,
                nom_entreprise: <?= json_encode(htmlspecialchars($_POST['nom_entreprise'] ?? '')) ?>,
                description: <?= json_encode(htmlspecialchars($_POST['description'] ?? '')) ?>,
                adresse_facturation: <?= json_encode(htmlspecialchars($_POST['adresse_facturation'] ?? '')) ?>,
                nom_contact: <?= json_encode(htmlspecialchars($_POST['nom_contact'] ?? '')) ?>,
                telephone: <?= json_encode(htmlspecialchars($_POST['telephone'] ?? '')) ?>,
                site_web: <?= json_encode(htmlspecialchars($_POST['site_web'] ?? '')) ?>,
                siret: <?= json_encode(htmlspecialchars($_POST['siret'] ?? '')) ?>
            };

            const commonFields = `
                <div class="form-section">
                    <h2>Informations de connexion</h2>
                    <div class="form-group">
                        <label for="email">Email professionnel :*</label>
                        <input type="email" id="email" name="email" required 
                               value="${previousValues.email}"
                               placeholder="votre.email@exemple.com"
                               maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur :*</label>
                        <input type="text" id="username" name="username" required 
                               value="${previousValues.username}"
                               placeholder="Choisissez un nom d'utilisateur"
                               maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe :*</label>
                        <div class="password-input-container">
                            <input type="password" id="password" name="password" required 
                                   placeholder="8 caractères minimum">
                            <span class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <small>Minimum 8 caractères, une majuscule et un chiffre</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmation du mot de passe :*</label>
                        <div class="password-input-container">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   required placeholder="Confirmez votre mot de passe">
                            <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
            `;

            let specificFields = '';

            if (role === "etudiant") {
                specificFields = `
                    <div class="form-section">
                        <h2>Informations personnelles</h2>
                        <div class="form-group">
                            <label for="nom">Nom :*</label>
                            <input type="text" id="nom" name="nom" required 
                                   value="${previousValues.nom}"
                                   placeholder="Votre nom de famille"
                                   maxlength="50">
                        </div>

                        <div class="form-group">
                            <label for="prenom">Prénom :*</label>
                            <input type="text" id="prenom" name="prenom" required 
                                   value="${previousValues.prenom}"
                                   placeholder="Votre prénom"
                                   maxlength="50">
                        </div>
                    </div>
                `;
            } else if (role === "entreprise") {
                specificFields = `
                    <div class="form-section">
                        <h2>Informations de l'entreprise</h2>
                        <div class="form-group">
                            <label for="nom_entreprise">Raison sociale :*</label>
                            <input type="text" id="nom_entreprise" name="nom_entreprise" required 
                                   value="${previousValues.nom_entreprise}"
                                   placeholder="Nom de votre entreprise"
                                   maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="description">Présentation de l'entreprise :*</label>
                            <textarea id="description" name="description" required 
                                    placeholder="Décrivez brièvement votre entreprise"
                                    maxlength="1000">${previousValues.description}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="adresse_facturation">Adresse de facturation :*</label>
                            <input type="text" id="adresse_facturation" name="adresse_facturation" 
                                   required value="${previousValues.adresse_facturation}" 
                                   placeholder="Adresse complète"
                                   maxlength="255">
                        </div>

                                                <div class="form-group">
                            <label for="siret">SIRET :*</label>
                            <input type="text" id="siret" name="siret" required 
                                   value="${previousValues.siret}" 
                                   placeholder="Numéro SIRET (14 chiffres)"
                                   maxlength="14">
                        </div>

                        <div class="form-group">
                            <label for="nom_contact">Nom du contact :</label>
                            <input type="text" id="nom_contact" name="nom_contact" 
                                   value="${previousValues.nom_contact}" 
                                   placeholder="Nom du contact au sein de l'entreprise" 
                                   maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="telephone">Numéro de téléphone :</label>
                            <input type="tel" id="telephone" name="telephone" 
                                   value="${previousValues.telephone}" 
                                   placeholder="Numéro de téléphone" 
                                   maxlength="20">
                        </div>

                        <div class="form-group">
                            <label for="site_web">Site web :</label>
                            <input type="url" id="site_web" name="site_web" 
                                   value="${previousValues.site_web}" 
                                   placeholder="www.votre-site.com" 
                                   maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="tva_intracommunautaire">Numéro de TVA intracommunautaire :</label>
                            <input type="text" id="tva_intracommunautaire" name="tva_intracommunautaire" 
                                   value="${previousValues.tva_intracommunautaire}" 
                                   placeholder="Numéro de TVA" 
                                   maxlength="20">
                        </div>
                    </div>
                `;
            }

            formContent.innerHTML = commonFields + specificFields;
        }

        updateForm();

        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const icon = passwordInput.parentElement.querySelector('.password-toggle i');
            
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

    <a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a>
</body>
</html>
