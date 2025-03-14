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

$isCertified = false;
$currentThemeColor = '#3498db'; // Couleur par défaut

if ($_SESSION['role'] == 'entreprise') {
    $stmt = $pdo->prepare("SELECT certification, theme_color FROM entreprises WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $isCertified = $result['certification'] == 1;
    $currentThemeColor = $result['theme_color'] ?? '#3498db';
}

$errors = [];
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des champs communs
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Validation du mot de passe si fourni
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
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $newFileName = uniqid() . '.' . $extension;
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadFile)) {
                $stmt = $pdo->prepare("UPDATE $table SET icone = ? WHERE id = ?");
                $stmt->execute([$newFileName, $_SESSION['user_id']]);
                
                $user = get_user($pdo, $_SESSION['user_id'], $table);
            } else {
                $errors[] = "Erreur lors du téléchargement de la photo.";
            }
        } else {
            $errors[] = "Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.";
        }
    }

    // Traitement du CV pour les étudiants
    if ($_SESSION['role'] == 'etudiant' && isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/public/uploads/cv/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['cv']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if ($extension === 'pdf') {
            $newFileName = uniqid() . '.pdf';
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['cv']['tmp_name'], $uploadFile)) {
                $stmt = $pdo->prepare("UPDATE etudiants SET cv = ? WHERE id = ?");
                $stmt->execute([$newFileName, $_SESSION['user_id']]);
                
                $user = get_user($pdo, $_SESSION['user_id'], $table);
            } else {
                $errors[] = "Erreur lors du téléchargement du CV.";
            }
        } else {
            $errors[] = "Format de fichier non autorisé. Utilisez uniquement le format PDF.";
        }
    }

    // Si pas d'erreurs, mise à jour des informations
    if (empty($errors)) {
        try {
            if ($_SESSION['role'] == 'entreprise') {
                $nom_entreprise = $_POST['nom_entreprise'] ?? '';
                $description = $_POST['description_entreprise'] ?? '';
                $siret = $_POST['siret'] ?? '';
                
                // Initialiser les champs de base
                $fields = ['username = ?', 'email = ?', 'nom = ?', 'description = ?', 'siret = ?'];
                $params = [$username, $email, $nom_entreprise, $description, $siret];

                // Ajouter le mot de passe si nécessaire
                if (!empty($new_password)) {
                    $fields[] = 'password = ?';
                    $params[] = $hashed_password;
                }

                // Gérer la couleur uniquement pour les entreprises certifiées
                if ($isCertified && isset($_POST['theme_color'])) {
                    $newColor = $_POST['theme_color'];
                    if (preg_match('/^#[a-fA-F0-9]{6}$/', $newColor)) {
                        $fields[] = 'theme_color = ?';
                        $params[] = $newColor;
                        $currentThemeColor = $newColor;
                    }
                }

                // Ajouter l'ID pour la clause WHERE
                $params[] = $_SESSION['user_id'];

                // Construire et exécuter la requête
                $sql = "UPDATE entreprises SET " . implode(', ', $fields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Recharger les données de l'utilisateur
                $user = get_user($pdo, $_SESSION['user_id'], $table);
                $successMessage = "Votre profil a été mis à jour avec succès !";
            } else {
                // Récupérer les champs spécifiques à l'étudiant
                $nom = $_POST['nom'];
                $prenom = $_POST['prenom'];

                if (!empty($new_password)) {
                    $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ?, password = ?, nom = ?, prenom = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $hashed_password, $nom, $prenom, $_SESSION['user_id']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE $table SET username = ?, email = ?, nom = ?, prenom = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $nom, $prenom, $_SESSION['user_id']]);
                }
            }

            $user = get_user($pdo, $_SESSION['user_id'], $table);
            $successMessage = "Votre profil a été mis à jour avec succès !";
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
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

        <div class="container">
            <form action="" method="post" enctype="multipart/form-data">
                <!-- Section Identifiants -->
                <div class="profile-section">
                    <label>Identifiants</label>
                    <div class="section-content">
                        <div class="input-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="new_password">Nouveau mot de passe (facultatif)</label>
                            <input type="password" id="new_password" name="new_password">
                            <p class="help-text">Laissez vide pour conserver le mot de passe actuel</p>
                        </div>
                    </div>
                </div>

                <!-- Section Photo -->
                <div class="profile-section">
                    <label>Photo de profil</label>
                    <div class="section-content">
                        <div class="photo-preview">
                            <?php if (!empty($user['icone'])): ?>
                                <img src="/Gestion_Stage/public/uploads/profil/<?php echo htmlspecialchars($user['icone']); ?>" alt="Photo de profil">
                            <?php else: ?>
                                <i class="fas <?php echo $_SESSION['role'] == 'etudiant' ? 'fa-user-graduate' : 'fa-building'; ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                            <p class="help-text">Formats acceptés : JPG, PNG, GIF</p>
                        </div>
                    </div>
                </div>

                <?php if ($_SESSION['role'] == 'etudiant'): ?>
                    <!-- Section Informations Étudiant -->
                    <div class="profile-section">
                        <label>Informations personnelles</label>
                        <div class="section-content">
                            <div class="input-group">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <!-- Section CV -->
                    <div class="profile-section">
                        <label>CV</label>
                        <div class="section-content">
                            <div class="cv-preview">
                                <?php if (!empty($user['cv'])): ?>
                                    <div class="current-cv">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>CV actuel</span>
                                        <a href="/Gestion_Stage/public/uploads/cv/<?php echo htmlspecialchars($user['cv']); ?>" 
                                           target="_blank" 
                                           class="view-cv-btn">
                                            <i class="fas fa-eye" style="color: white;"></i> Voir le CV
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p class="no-cv">Aucun CV téléchargé</p>
                                <?php endif; ?>
                            </div>
                            <div class="upload-controls">
                                <input type="file" id="cv" name="cv" accept=".pdf">
                                <p class="help-text">Format accepté: PDF uniquement. Taille maximale: 2MB</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Section Informations Entreprise -->
                    <div class="profile-section">
                        <label>Informations de l'entreprise</label>
                        <div class="section-content">
                            <div class="input-group">
                                <label for="nom_entreprise">Nom de l'entreprise</label>
                                <input type="text" id="nom_entreprise" name="nom_entreprise" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="description_entreprise">Description</label>
                                <textarea id="description_entreprise" name="description_entreprise" rows="4" required><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="input-group">
                                <label for="siret">SIRET</label>
                                <input type="text" id="siret" name="siret" value="<?php echo htmlspecialchars($user['siret'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'entreprise' && $isCertified): ?>
                    <div class="theme-color-section">
                        <label>Couleur du profil</label>
                        <div class="color-picker-wrapper">
                            <div class="color-preview-box" onclick="document.getElementById('theme_color').click()">
                                <div class="color-display" style="background-color: <?php echo htmlspecialchars($currentThemeColor ?? '#3498db'); ?>"></div>
                                <div class="color-info">
                                    <span class="color-name">Couleur actuelle</span>
                                    <span class="color-value"><?php echo htmlspecialchars($currentThemeColor ?? '#3498db'); ?></span>
                                </div>
                            </div>
                            <input type="color" 
                                id="theme_color" 
                                name="theme_color" 
                                value="<?php echo htmlspecialchars($currentThemeColor ?? '#3498db'); ?>"
                                class="color-picker-input">
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="submit-btn">Mettre à jour le profil</button>
            </form>
        </div>

        <?php if ($_SESSION['role'] == 'entreprise'): ?>
            <p>
                <a href="/Gestion_Stage/app/views/company_profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="view-profile-button">
                    <i class="fas fa-eye"></i> Visualiser mon profil
                </a>
            </p>
        <?php endif; ?>

        <p><a class="index-button" href="/Gestion_Stage/app/views/home.php">Retour à l'espace personnel</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorPicker = document.getElementById('theme_color');
            const colorDisplay = document.querySelector('.color-display');
            const colorValue = document.querySelector('.color-value');
            
            if (colorPicker && colorDisplay && colorValue) {
                colorPicker.addEventListener('input', function(e) {
                    const newColor = e.target.value;
                    colorDisplay.style.backgroundColor = newColor;
                    colorValue.textContent = newColor;
                });
            }
        });
    </script>
</body>
</html>
