<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start(); // Add this line to prevent header issues

require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header('Location: /Gestion_Stage/app/views/auth/login.php');
    exit();
}

// Récupérer les informations de l'entreprise
$stmt = $pdo->prepare("SELECT * FROM entreprises WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

$currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Modifier la partie du traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialiser le tableau form_data s'il n'existe pas
    if (!isset($_SESSION['form_data'])) {
        $_SESSION['form_data'] = [];
    }

    if (isset($_POST['next_step']) || isset($_POST['prev_step'])) {
        // Sauvegarder les données du formulaire actuel
        $_SESSION['form_data'] = array_merge($_SESSION['form_data'], $_POST);
        
        // Déterminer la prochaine étape
        $nextStep = isset($_POST['next_step']) ? $_POST['next_step'] : $_POST['prev_step'];
        header("Location: ?step=" . $nextStep);
        exit();
    }

    if (isset($_POST['final_submit'])) {
        try {
            // Vérification des champs requis
            $required_fields = [
                'titre', 'description', 'email_contact', 'date_debut', 
                'date_fin', 'domaine', 'remuneration', 'ville', 
                'code_postal', 'region', 'departement'
            ];

            $missing_fields = [];
            foreach ($required_fields as $field) {
                if (empty($_SESSION['form_data'][$field])) {
                    $missing_fields[] = $field;
                }
            }

            if (!empty($missing_fields)) {
                throw new Exception("Les champs suivants sont requis : " . implode(", ", $missing_fields));
            }

            // Dans la section où vous préparez les données pour l'insertion
            if (isset($_SESSION['form_data']['remuneration'])) {
                if ($_SESSION['form_data']['remuneration'] === 'autre' && isset($_SESSION['form_data']['remuneration_autre'])) {
                    $remuneration = $_SESSION['form_data']['remuneration_autre'];
                } else {
                    $remuneration = $_SESSION['form_data']['remuneration'];
                }
            } else {
                $remuneration = null;
            }

            // Préparer les données pour l'insertion
            $data = [
                'entreprise_id' => $_SESSION['user_id'],
                'titre' => $_SESSION['form_data']['titre'],
                'description' => $_SESSION['form_data']['description'],
                'email_contact' => $_SESSION['form_data']['email_contact'],
                'lien_candidature' => $_SESSION['form_data']['lien_candidature'] ?? null,
                'date_debut' => $_SESSION['form_data']['date_debut'],
                'date_fin' => $_SESSION['form_data']['date_fin'],
                'domaine' => $_SESSION['form_data']['domaine'],
                'remuneration' => $remuneration,
                'pays' => $_SESSION['form_data']['pays'] ?? 'France',
                'ville' => $_SESSION['form_data']['ville'],
                'code_postal' => $_SESSION['form_data']['code_postal'],
                'region' => $_SESSION['form_data']['region'],
                'departement' => $_SESSION['form_data']['departement'],
                'lieu' => $_SESSION['form_data']['lieu'] ?? null,
                'mode_stage' => $_SESSION['form_data']['mode_stage'] ?? 'présentiel'
            ];

            $stmt = $pdo->prepare("INSERT INTO offres_stages (
                entreprise_id, titre, description, email_contact, lien_candidature,
                date_debut, date_fin, domaine, remuneration,
                pays, ville, code_postal, region, departement, lieu, mode_stage
            ) VALUES (
                :entreprise_id, :titre, :description, :email_contact, :lien_candidature,
                :date_debut, :date_fin, :domaine, :remuneration,
                :pays, :ville, :code_postal, :region, :departement, :lieu, :mode_stage
            )");
            
            $stmt->execute($data);

            // Nettoyer la session et rediriger
            unset($_SESSION['form_data']);
            $_SESSION['success_message'] = "Offre de stage publiée avec succès!";
            header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
            // Retourner à l'étape appropriée en cas d'erreur
            if (strpos($error, 'titre') !== false || strpos($error, 'description') !== false) {
                header("Location: ?step=2");
                exit();
            }
            if (strpos($error, 'region') !== false || strpos($error, 'ville') !== false) {
                header("Location: ?step=3");
                exit();
            }
        }
    } else {
        // Navigation entre les étapes
        $nextStep = $currentStep + 1;
        if ($nextStep <= 3) {
            header("Location: ?step=$nextStep");
            exit();
        }
    }
}

// Ajouter cette fonction helper améliorée
function getFormValue($field, $default = '') {
    return isset($_SESSION['form_data'][$field]) ? $_SESSION['form_data'][$field] : $default;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Publier une offre de stage</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_post_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <div class="steps">
            <div class="step <?= $currentStep >= 1 ? 'active' : '' ?>">1</div>
            <div class="step-connector <?= $currentStep >= 2 ? 'active' : '' ?>"></div>
            <div class="step <?= $currentStep >= 2 ? 'active' : '' ?>">2</div>
            <div class="step-connector <?= $currentStep >= 3 ? 'active' : '' ?>"></div>
            <div class="step <?= $currentStep >= 3 ? 'active' : '' ?>">3</div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>)): ?>
        <?php endif; ?>

        <form method="POST" action="" class="multi-step-form" id="internshipForm">
            
            <?php if ($currentStep === 1): ?>
                <div class="step-content">
                    <h2>Étape 1 : Informations sur l'entreprise</h2>
                    
                    <div class="form-group">
                        <label for="nom_entreprise">Nom de l'entreprise</label>
                        <input type="text" id="nom_entreprise" name="nom_entreprise" 
                               value="<?= htmlspecialchars($entreprise['nom']) ?>" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="email_contact">Email de contact</label>
                        <input type="email" id="email_contact" name="email_contact" 
                               value="<?= htmlspecialchars(getFormValue('email_contact')) ?>"
                               placeholder="contact@entreprise.com" required>
                    </div>

                    <div class="form-group">
                        <label for="description_entreprise">Description de l'entreprise*</label>
                        <textarea id="description_entreprise" name="description_entreprise" 
                                maxlength="500" required><?= htmlspecialchars($entreprise['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo de l'entreprise</label>
                        <input type="file" id="logo" name="logo" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="site_web">Site web de l'entreprise</label>
                        <input type="url" id="site_web" name="site_web" 
                               value="<?= htmlspecialchars($entreprise['site_web']) ?>" 
                               placeholder="https://www.entreprise.com">
                    </div>

                    <div class="form-navigation">
                        <button type="submit" name="next_step" value="2">Suivant</button>
                    </div>
                </div>

            <?php elseif ($currentStep === 2): ?>
                <div class="step-content">
                    <h2>Étape 2 : Détails du stage</h2>
                    
                    <div class="form-group">
                        <label for="titre">Titre de l'offre</label>
                        <input type="text" id="titre" name="titre" required maxlength="200"
                               value="<?= htmlspecialchars(getFormValue('titre')) ?>"
                               placeholder="Ex: Assistant de recherche (6 mois)">
                    </div>

                    <div class="form-group">
                        <label for="description">Description du stage</label>
                        <textarea id="description" name="description" required 
                                minlength="200" placeholder="Décrivez les missions, objectifs..."><?= htmlspecialchars(getFormValue('description')) ?></textarea>
                        <small>Minimum 200 caractères requis</small>
                    </div>

                    <div class="form-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut" required
                               value="<?= htmlspecialchars(getFormValue('date_debut')) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin" required
                               value="<?= htmlspecialchars(getFormValue('date_fin')) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="domaine">Domaine</label>
                        <select id="domaine" name="domaine" required>
                            <option value="">Sélectionner un domaine</option>
                            <optgroup label="Informatique">
                                <option value="developpement_web" <?= getFormValue('domaine') === 'developpement_web' ? 'selected' : '' ?>>Développement Web</option>
                                <option value="developpement_mobile" <?= getFormValue('domaine') === 'developpement_mobile' ? 'selected' : '' ?>>Développement Mobile</option>
                                <option value="reseaux">Réseaux</option>
                                <option value="cybersecurite">Cybersécurité</option>
                            </optgroup>
                            <optgroup label="Commerce">
                                <option value="marketing_digital">Marketing Digital</option>
                                <option value="commerce_international">Commerce International</option>
                                <option value="vente">Vente</option>
                            </optgroup>
                            <optgroup label="Autres">
                                <option value="finance">Finance</option>
                                <option value="ressources_humaines">Ressources Humaines</option>
                                <option value="communication">Communication</option>
                                <option value="logistique">Logistique</option>
                                <option value="autre">Autre</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remuneration">Rémunération mensuelle</label>
                        <select id="remuneration" name="remuneration" required onchange="toggleAutreMontant()">
                            <option value="">Sélectionner une rémunération</option>
                            <option value="417" <?= getFormValue('remuneration') === '417' ? 'selected' : '' ?>>Minimum légal (417€)</option>
                            <option value="500" <?= getFormValue('remuneration') === '500' ? 'selected' : '' ?>>500€</option>
                            <option value="600" <?= getFormValue('remuneration') === '600' ? 'selected' : '' ?>>600€</option>
                            <option value="700" <?= getFormValue('remuneration') === '700' ? 'selected' : '' ?>>700€</option>
                            <option value="800" <?= getFormValue('remuneration') === '800' ? 'selected' : '' ?>>800€</option>
                            <option value="900" <?= getFormValue('remuneration') === '900' ? 'selected' : '' ?>>900€</option>
                            <option value="1000" <?= getFormValue('remuneration') === '1000' ? 'selected' : '' ?>>1000€</option>
                            <option value="autre" <?= getFormValue('remuneration') === 'autre' ? 'selected' : '' ?>>Autre montant</option>
                        </select>
                        <div id="autre_montant_container" style="display: none; margin-top: 10px;">
                            <input type="number" 
                                   id="remuneration_autre" 
                                   name="remuneration_autre" 
                                   min="417" 
                                   step="1" 
                                   placeholder="Saisir un montant en euros"
                                   value="<?= getFormValue('remuneration_autre') ?>"
                                   oninput="updateRemuneration(this.value)">
                            <small>Minimum légal : 417€</small>
                        </div>
                        <!-- Champ caché pour stocker la valeur finale de la rémunération -->
                        <input type="hidden" id="remuneration_hidden" name="remuneration" 
                               value="<?= htmlspecialchars(getFormValue('remuneration')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="mode_stage">Mode de stage</label>
                        <select id="mode_stage" name="mode_stage" required>
                            <option value="présentiel">Présentiel</option>
                            <option value="distanciel">Distanciel</option>
                        </select>
                    </div>

                    <input type="hidden" name="lieu" value="">
                    <input type="hidden" name="lien_candidature" value="">
                    <!-- Ajouter les champs cachés de l'étape 1 -->
                    <input type="hidden" name="email_contact" value="<?= htmlspecialchars(getFormValue('email_contact')) ?>">

                    <div class="form-navigation">
                        <button type="submit" name="prev_step" value="1">Précédent</button>
                        <button type="submit" name="next_step" value="3">Suivant</button>
                    </div>
                </div>

            <?php elseif ($currentStep === 3): ?>
                <div class="step-content">
                    <h2>Étape 3 : Localisation</h2>
                    
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <input type="text" id="pays" name="pays" value="France" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="region">Région</label>
                        <select id="region" name="region" required onchange="updateDepartements()">
                            <option value="">Sélectionner une région</option>
                            <option value="Auvergne-Rhône-Alpes">Auvergne-Rhône-Alpes</option>
                            <option value="Bourgogne-Franche-Comté">Bourgogne-Franche-Comté</option>
                            <option value="Bretagne">Bretagne</option>
                            <option value="Centre-Val de Loire">Centre-Val de Loire</option>
                            <option value="Corse">Corse</option>
                            <option value="Grand Est">Grand Est</option>
                            <option value="Hauts-de-France">Hauts-de-France</option>
                            <option value="Île-de-France">Île-de-France</option>
                            <option value="Normandie">Normandie</option>
                            <option value="Nouvelle-Aquitaine">Nouvelle-Aquitaine</option>
                            <option value="Occitanie">Occitanie</option>
                            <option value="Pays de la Loire">Pays de la Loire</option>
                            <option value="Provence-Alpes-Côte d'Azur">Provence-Alpes-Côte d'Azur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="departement">Département</label>
                        <select id="departement" name="departement" required onchange="updateVilles()">
                            <option value="">Sélectionner d'abord une région</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <select id="ville" name="ville" required onchange="updateCodePostal()">
                            <option value="">Sélectionner d'abord un département</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code_postal">Code postal</label>
                        <input type="text" id="code_postal" name="code_postal" required 
                               pattern="[0-9]{5}" placeholder="Ex: 58000">
                    </div>

                    <!-- Ajouter les champs cachés des étapes précédentes -->
                    <input type="hidden" name="email_contact" value="<?= htmlspecialchars(getFormValue('email_contact')) ?>">
                    <input type="hidden" name="titre" value="<?= htmlspecialchars(getFormValue('titre')) ?>">
                    <input type="hidden" name="description" value="<?= htmlspecialchars(getFormValue('description')) ?>">
                    <input type="hidden" name="date_debut" value="<?= htmlspecialchars(getFormValue('date_debut')) ?>">
                    <input type="hidden" name="date_fin" value="<?= htmlspecialchars(getFormValue('date_fin')) ?>">
                    <input type="hidden" name="domaine" value="<?= htmlspecialchars(getFormValue('domaine')) ?>">
                    <input type="hidden" name="remuneration" value="<?= htmlspecialchars(getFormValue('remuneration')) ?>">
                    <input type="hidden" name="mode_stage" value="<?= htmlspecialchars(getFormValue('mode_stage')) ?>">

                    <div class="form-navigation">
                        <button type="submit" name="prev_step" value="2">Précédent</button>
                        <button type="submit" name="final_submit">Publier l'offre</button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <p class="index-button"><a class="index-button" href="/Gestion_Stage/app/views/home.php"><i class="fas fa-arrow-left"></i> Retour à l'espace personnel</a></p>

    <script src="/Gestion_Stage/public/assets/js/location.js"></script>
    <script src="/Gestion_Stage/public/assets/js/remuneration.js"></script>
</body>
</html>