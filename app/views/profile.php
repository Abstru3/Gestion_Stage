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
    $description_entreprise = $_POST['description_entreprise'];  // Récupération de la description

    // Vérification des exigences du mot de passe
    if (!empty($new_password)) {
        if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    // Traitement de la photo de profil
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/public/uploads/profil/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['profile_photo']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Vérifier le type de fichier
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Générer un nom de fichier unique
            $newFileName = uniqid() . '.' . $extension;
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadFile)) {
                // Mettre à jour la base de données avec le nouveau nom de fichier
                $stmt = $pdo->prepare("UPDATE $table SET icone = ? WHERE id = ?");
                $stmt->execute([$newFileName, $_SESSION['user_id']]);
                
                // Rafraîchir les données de l'utilisateur
                $user = get_user($pdo, $_SESSION['user_id'], $table);
            } else {
                $errors[] = "Erreur lors du téléchargement de la photo.";
            }
        } else {
            $errors[] = "Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.";
        }
    }

    // Mise à jour de la description de l'entreprise
    if (empty($errors)) {
        if (!empty($new_password)) {
            $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ?, password = ?, description = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashed_password, $description_entreprise, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ?, description = ? WHERE id = ?");
            $stmt->execute([$username, $email, $description_entreprise, $_SESSION['user_id']]);
        }

        // Rafraîchir les données de l'utilisateur
        $user = get_user($pdo, $_SESSION['user_id'], $table);

        // Message de succès
        $successMessage = "Votre profil a été mis à jour avec succès !";
    }
}

$isCertified = false;
if ($_SESSION['role'] == 'entreprise') {
    $stmt = $pdo->prepare("SELECT certification, theme_color FROM entreprises WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $isCertified = $result['certification'] == 1;
    $currentThemeColor = $result['theme_color'];
}

// Traitement de la mise à jour de la couleur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['theme_color'])) {
    $newColor = $_POST['theme_color'];
    // Validation du format hexadécimal
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $newColor)) {
        $stmt = $pdo->prepare("UPDATE entreprises SET theme_color = ? WHERE id = ?");
        $stmt->execute([$newColor, $_SESSION['user_id']]);
        $currentThemeColor = $newColor;
        $successMessage = "La couleur du thème a été mise à jour avec succès !";
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
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <?php if (!empty($user['icone'])): ?>
                <img src="/Gestion_Stage/public/uploads/profil/<?php echo htmlspecialchars($user['icone']); ?>" alt="Photo de profil" class="profile-photo">
            <?php else: ?>
                <i class="<?php echo $_SESSION['role'] == 'etudiant' ? 'fas fa-user-graduate' : 'fas fa-building'; ?> profile-icon"></i>
            <?php endif; ?>
            <h1>Mon profil</h1>
        </div>

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

        <form action="" method="post" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="new_password">Nouveau mot de passe (laisser vide pour ne pas changer):</label>
            <input type="password" id="new_password" name="new_password">

            <div class="photo-upload">
                <label for="profile_photo">Photo de profil:</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                <p class="help-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</p>
            </div>

            <?php if ($_SESSION['role'] == 'etudiant'): ?>
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
                <label for="nom_entreprise">Nom de l'entreprise:</label>
                <input type="text" id="nom_entreprise" name="nom_entreprise" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                
                <label for="description_entreprise">Description de l'entreprise:</label>
                <textarea id="description_entreprise" name="description_entreprise" rows="4" required><?php echo htmlspecialchars($user['description']); ?></textarea>
                
                <label for="siret">SIRET:</label>
                <input type="text" id="siret" name="siret" value="<?php echo htmlspecialchars($user['siret']); ?>" required>
            <?php endif; ?>

            <button type="submit">Mettre à jour le profil</button>

            <?php if ($_SESSION['role'] == 'entreprise' && $isCertified): ?>
                <div class="theme-color-section">
                <br><label for="profile_photo">Couleur du profil</label>
                    <form action="" method="post" class="color-picker-form">
                        <div class="color-picker-container">
                            <label for="theme_color">Sélectionnez une couleur :</label>
                            <input type="color" 
                                id="theme_color" 
                                name="theme_color" 
                                value="<?php echo htmlspecialchars($currentThemeColor ?? '#3498db'); ?>"
                                class="color-picker-input">
                        </div>
                        <button type="submit" class="color-submit-btn">Appliquer la couleur</button>
                    </form>
                    <?php if (isset($currentThemeColor)): ?>
                        <p class="current-color">Couleur actuelle : <span><?php echo htmlspecialchars($currentThemeColor); ?></span></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </form>

        <?php if ($_SESSION['role'] == 'entreprise'): ?>
            <p>
                <a href="/Gestion_Stage/app/views/company_profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="view-profile-button">
                    <i class="fas fa-eye"></i> Visualiser mon profil
                </a>
            </p>
        <?php endif; ?>

        <p><a class="index-button" href="/Gestion_Stage/app/views/home.php">Retour à l'espace personnel</a></p>
    </div>
</body>
</html>
