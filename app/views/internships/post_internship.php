<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header('Location: /Gestion_Stage/app/views/auth/login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM entreprises WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

// Cette partie pose problème car elle est traitée après les gestionnaires de POST
// Déplaçons ce code plus tôt, avant tout traitement POST

$currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = [];
}

// Modifier cette partie pour s'assurer que le paramètre GET type est prioritaire sur la session
// et qu'il est pris en compte même après un POST
if (isset($_GET['type']) && in_array($_GET['type'], ['stage', 'alternance'])) {
    $_SESSION['form_data']['type_offre'] = $_GET['type'];
} elseif (!isset($_SESSION['form_data']['type_offre'])) {
    $_SESSION['form_data']['type_offre'] = 'stage'; // Valeur par défaut
}

// Puis traiter les soumissions de formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type_offre'])) {
        $_SESSION['form_data']['type_offre'] = $_POST['type_offre'];
    }
    
    $_SESSION['form_data'] = array_merge($_SESSION['form_data'], $_POST);

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/public/uploads/logos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['logo']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $newFileName = uniqid() . '.' . $extension;
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                $_SESSION['form_data']['logo'] = 'uploads/logos/' . $newFileName;
            } else {
                $error = "Erreur lors du téléchargement du logo.";
            }
        } else {
            $error = "Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.";
        }
    }

    if (isset($_POST['next_step'])) {
        header("Location: ?step=" . (int) $_POST['next_step']);
        exit();
    }

    if (isset($_POST['prev_step'])) {
        header("Location: ?step=" . (int) $_POST['prev_step']);
        exit();
    }

    if (isset($_POST['final_submit'])) {
        try {
            // Champs requis adaptés selon le type d'offre
            $_fields = ['titre', 'description', 'email_contact', 'date_debut', 'domaine', 'remuneration', 'ville', 'code_postal', 'region', 'departement'];
            
            // Ajout de validation pour les champs spécifiques à l'alternance
            if ($_SESSION['form_data']['type_offre'] === 'alternance') {
                $_fields[] = 'niveau_etude';
                $_fields[] = 'type_contrat';
                $_fields[] = 'rythme_alternance';
            }

            foreach ($_fields as $field) {
                if (empty($_SESSION['form_data'][$field])) {
                    throw new Exception("Le champ '$field' est requis.");
                }
            }

            // Pour les alternances, séparer le type de rémunération et le montant
            $type_remuneration = null;
            if ($_SESSION['form_data']['type_offre'] === 'alternance' && 
                strpos($_SESSION['form_data']['remuneration'], 'smic') !== false) {
                $type_remuneration = $_SESSION['form_data']['remuneration'];
                
                // Valeurs approximatives pour 2025
                switch ($type_remuneration) {
                    case 'smic27': $remuneration = 486; break; // 27% de 1800€
                    case 'smic43': $remuneration = 774; break; // 43% de 1800€
                    case 'smic53': $remuneration = 954; break; // 53% de 1800€
                    case 'smic100': $remuneration = 1800; break; // 100% de 1800€
                    default: $remuneration = null;
                }
            } else {
                $remuneration = $_SESSION['form_data']['remuneration'] === 'autre' 
                    ? ($_SESSION['form_data']['remuneration_autre'] ?? null) 
                    : $_SESSION['form_data']['remuneration'];
            }

            $data = [
                'entreprise_id' => $_SESSION['user_id'],
                'titre' => $_SESSION['form_data']['titre'],
                'description' => $_SESSION['form_data']['description'],
                'email_contact' => $_SESSION['form_data']['email_contact'],
                'lien_candidature' => $_SESSION['form_data']['lien_candidature'] ?? null,
                'date_debut' => $_SESSION['form_data']['date_debut'],
                'date_fin' => $_SESSION['form_data']['date_fin'] ?? null,
                'domaine' => $_SESSION['form_data']['domaine'],
                'remuneration' => $remuneration,
                'type_remuneration' => $type_remuneration,
                'pays' => $_SESSION['form_data']['pays'] ?? 'France',
                'ville' => $_SESSION['form_data']['ville'],
                'code_postal' => $_SESSION['form_data']['code_postal'],
                'region' => $_SESSION['form_data']['region'],
                'departement' => $_SESSION['form_data']['departement'],
                'mode_stage' => $_SESSION['form_data']['mode_stage'] ?? 'présentiel',
                'logo' => $_SESSION['form_data']['logo'] ?? null,
                'lieu' => $_SESSION['form_data']['lieu'] ?? null,
                'type_offre' => $_SESSION['form_data']['type_offre']
            ];
            
            // Ajout des données spécifiques à l'alternance
            if ($_SESSION['form_data']['type_offre'] === 'alternance') {
                $data['niveau_etude'] = $_SESSION['form_data']['niveau_etude'];
                $data['type_contrat'] = $_SESSION['form_data']['type_contrat'];
                $data['rythme_alternance'] = $_SESSION['form_data']['rythme_alternance'];
                $data['formation_visee'] = $_SESSION['form_data']['formation_visee'] ?? null;
                $data['ecole_partenaire'] = $_SESSION['form_data']['ecole_partenaire'] ?? null;
            }

            $fields = array_keys($data);
            $placeholders = array_map(function($field) { return ":$field"; }, $fields);
            
            $sql = "INSERT INTO offres_stages (" . implode(", ", $fields) . ") 
                    VALUES (" . implode(", ", $placeholders) . ")";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            unset($_SESSION['form_data']);
            
            // Message de succès adapté selon le type d'offre
            if ($_SESSION['form_data']['type_offre'] === 'alternance') {
                $_SESSION['success_message'] = "Offre d'alternance publiée avec succès!";
            } else {
                $_SESSION['success_message'] = "Offre de stage publiée avec succès!";
            }
            
            header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - <?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'Publier une offre d\'alternance' : 'Publier une offre de stage' ?></title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_post_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
    <style>
        
    </style>
</head>
<body class="<?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'alternance-mode' : '' ?>">

    <div class="container">
    <h1><?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'Publier une offre d\'alternance' : 'Publier une offre de stage' ?></h1>
    
    <!-- Sélecteur du type d'offre -->
    <form method="POST" action="" class="offre-type-selector">
        <button type="submit" style="color:#3498db" name="type_offre" value="stage" class="offre-type-btn <?= $_SESSION['form_data']['type_offre'] === 'stage' ? 'active' : '' ?>">Stage</button>
        <button type="submit" style="color:#3498db" name="type_offre" value="alternance" class="offre-type-btn <?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'active' : '' ?>">Alternance</button>
    </form>

    <?php if($_SESSION['form_data']['type_offre'] === 'alternance'): ?>
        <div class="alternance-info-box">
            <h4>Information importante</h4>
            <p>En publiant une offre d'alternance, vous vous engagez à recruter un(e) alternant(e) dans le cadre d'un contrat d'apprentissage ou de professionnalisation.</p>
            <p>Ces contrats combinent formation théorique et pratique en entreprise avec des avantages fiscaux pour l'employeur.</p>
        </div>
    <?php endif; ?>

    <div class="steps">
            <div class="step <?= $currentStep >= 1 ? 'active' : '' ?>">1</div>
            <div class="step-connector <?= $currentStep >= 2 ? 'active' : '' ?>"></div>
            <div class="step <?= $currentStep >= 2 ? 'active' : '' ?>">2</div>
            <div class="step-connector <?= $currentStep >= 3 ? 'active' : '' ?>"></div>
            <div class="step <?= $currentStep >= 3 ? 'active' : '' ?>">3</div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="POST" action="" class="multi-step-form" id="internshipForm" enctype="multipart/form-data">
            <input type="hidden" name="type_offre" value="<?= $_SESSION['form_data']['type_offre'] ?>">
            
            <?php if ($currentStep === 1): ?>
                <div class="step-content">
                    <h2>Étape 1 : Informations sur l'entreprise</h2>
                    
                    <div class="form-group">
                        <label for="nom_entreprise">Nom de l'entreprise*</label>
                        <input type="text" id="nom_entreprise" name="nom_entreprise" 
                               value="<?= htmlspecialchars($entreprise['nom']) ?>"  maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="email_contact">Email de contact*</label>
                        <input type="email" id="email_contact" name="email_contact" 
                               value="<?= htmlspecialchars(getFormValue('email_contact')) ?>"
                               placeholder="contact@entreprise.com" >
                    </div>

                    <div class="form-group">
                        <label for="description_entreprise">Description de l'entreprise*</label>
                        <textarea id="description_entreprise" name="description_entreprise" 
                                maxlength="500" ><?= htmlspecialchars($entreprise['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo de l'entreprise</label>
                        <div class="logo-preview">
                            
                        </div>
                        <input type="file" id="logo" name="logo" accept="image/*">
                        <p class="help-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</p>
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
                    <h2>Étape 2 : Détails de <?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'l\'alternance' : 'du stage' ?></h2>
                    
                    <div class="form-group">
                        <label for="titre">Titre de l'offre*</label>
                        <input type="text" id="titre" name="titre"  maxlength="200"
                               value="<?= htmlspecialchars(getFormValue('titre')) ?>"
                               placeholder="<?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'Ex: Alternant développeur web' : 'Ex: Assistant de recherche (6 mois)' ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description <?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'de l\'alternance' : 'du stage' ?>*</label>
                        <textarea id="description" name="description"  
                                minlength="200" placeholder="<?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'Décrivez les missions, objectifs de l\'alternance...' : 'Décrivez les missions, objectifs du stage...' ?>"><?= htmlspecialchars(getFormValue('description')) ?></textarea>
                        <small>Minimum 200 caractères requis</small>
                    </div>

                    <div class="form-group">
                        <label for="date_debut">Date de début*</label>
                        <input type="date" id="date_debut" name="date_debut" 
                               value="<?= htmlspecialchars(getFormValue('date_debut')) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_fin">Date de fin <?= $_SESSION['form_data']['type_offre'] === 'alternance' ? '(estimée)' : '' ?></label>
                        <input type="date" id="date_fin" name="date_fin"
                               value="<?= htmlspecialchars(getFormValue('date_fin')) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>


                    <div class="form-group">
                        <label for="domaine">Domaine*</label>
                        <select id="domaine" name="domaine" >
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

                    <?php if($_SESSION['form_data']['type_offre'] === 'alternance'): ?>
                    <!-- Options de rémunération spécifiques à l'alternance -->
                    <div class="form-group">
                        <label for="remuneration">Rémunération mensuelle*</label>
                        <select id="remuneration" name="remuneration" onchange="toggleAutreMontant()">
                            <option value="">Sélectionner une rémunération</option>
                            <option value="smic27" <?= getFormValue('remuneration') === 'smic27' ? 'selected' : '' ?>>27% du SMIC (moins de 18 ans)</option>
                            <option value="smic43" <?= getFormValue('remuneration') === 'smic43' ? 'selected' : '' ?>>43% du SMIC (18-20 ans)</option>
                            <option value="smic53" <?= getFormValue('remuneration') === 'smic53' ? 'selected' : '' ?>>53% du SMIC (21-25 ans)</option>
                            <option value="smic100" <?= getFormValue('remuneration') === 'smic100' ? 'selected' : '' ?>>100% du SMIC (26 ans et plus)</option>
                            <option value="autre" <?= getFormValue('remuneration') === 'autre' ? 'selected' : '' ?>>Autre montant</option>
                        </select>
                        <div id="autre_montant_container" style="display: <?= getFormValue('remuneration') === 'autre' ? 'block' : 'none' ?>; margin-top: 10px;">
                            <input type="number" 
                                   id="remuneration_autre" 
                                   name="remuneration_autre" 
                                   min="500" 
                                   step="1" 
                                   placeholder="Saisir un montant en euros"
                                   value="<?= getFormValue('remuneration_autre') ?>"
                                   oninput="updateRemuneration(this.value)">
                        </div>
                        <small>La rémunération en alternance dépend de l'âge et du niveau d'études</small>
                    </div>
                    <?php else: ?>
                    <!-- Rémunération pour les stages -->
                    <div class="form-group">
                        <label for="remuneration">Rémunération mensuelle*</label>
                        <select id="remuneration" name="remuneration" onchange="toggleAutreMontant()">
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
                        <div id="autre_montant_container" style="display: <?= getFormValue('remuneration') === 'autre' ? 'block' : 'none' ?>; margin-top: 10px;">
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
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="mode_stage"><?= $_SESSION['form_data']['type_offre'] === 'alternance' ? 'Mode de travail' : 'Mode de stage' ?>*</label>
                        <select id="mode_stage" name="mode_stage" >
                            <option value="présentiel" <?= getFormValue('mode_stage') === 'présentiel' ? 'selected' : '' ?>>Présentiel</option>
                            <option value="distanciel" <?= getFormValue('mode_stage') === 'distanciel' ? 'selected' : '' ?>>Distanciel</option>
                            <option value="hybride" <?= getFormValue('mode_stage') === 'hybride' ? 'selected' : '' ?>>Hybride</option>
                        </select>
                    </div>

                    <?php if($_SESSION['form_data']['type_offre'] === 'alternance'): ?>
                    <!-- Champs spécifiques à l'alternance -->
                    <div class="form-group">
                        <label for="niveau_etude">Niveau d'étude requis*</label>
                        <select id="niveau_etude" name="niveau_etude" required>
                            <option value="">Sélectionner un niveau</option>
                            <option value="bac+2" <?= getFormValue('niveau_etude') === 'bac+2' ? 'selected' : '' ?>>Bac+2</option>
                            <option value="bac+3" <?= getFormValue('niveau_etude') === 'bac+3' ? 'selected' : '' ?>>Bac+3</option>
                            <option value="bac+5" <?= getFormValue('niveau_etude') === 'bac+5' ? 'selected' : '' ?>>Bac+5</option>
                            <option value="autre" <?= getFormValue('niveau_etude') === 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_contrat">Type de contrat*</label>
                        <select id="type_contrat" name="type_contrat" required>
                            <option value="">Sélectionner un type de contrat</option>
                            <option value="apprentissage" <?= getFormValue('type_contrat') === 'apprentissage' ? 'selected' : '' ?>>Contrat d'apprentissage</option>
                            <option value="professionnalisation" <?= getFormValue('type_contrat') === 'professionnalisation' ? 'selected' : '' ?>>Contrat de professionnalisation</option>
                        </select>
                        <small>Le contrat d'apprentissage concerne les étudiants de moins de 30 ans</small>
                    </div>

                    <div class="form-group">
                        <label for="rythme_alternance">Rythme d'alternance*</label>
                        <select id="rythme_alternance" name="rythme_alternance" required>
                            <option value="">Sélectionner un rythme</option>
                            <option value="1sem_1sem" <?= getFormValue('rythme_alternance') === '1sem_1sem' ? 'selected' : '' ?>>1 semaine entreprise / 1 semaine école</option>
                            <option value="2sem_1sem" <?= getFormValue('rythme_alternance') === '2sem_1sem' ? 'selected' : '' ?>>2 semaines entreprise / 1 semaine école</option>
                            <option value="3sem_1sem" <?= getFormValue('rythme_alternance') === '3sem_1sem' ? 'selected' : '' ?>>3 semaines entreprise / 1 semaine école</option>
                            <option value="1mois_1sem" <?= getFormValue('rythme_alternance') === '1mois_1sem' ? 'selected' : '' ?>>1 mois entreprise / 1 semaine école</option>
                            <option value="autre" <?= getFormValue('rythme_alternance') === 'autre' ? 'selected' : '' ?>>Autre rythme</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="formation_visee">Formation visée</label>
                        <input type="text" id="formation_visee" name="formation_visee"
                               value="<?= htmlspecialchars(getFormValue('formation_visee')) ?>"
                               placeholder="Ex: BTS SIO, Licence Pro développement web, etc.">
                    </div>

                    <div class="form-group">
                        <label for="ecole_partenaire">École partenaire (optionnel)</label>
                        <input type="text" id="ecole_partenaire" name="ecole_partenaire"
                               value="<?= htmlspecialchars(getFormValue('ecole_partenaire')) ?>"
                               placeholder="Ex: IUT de Dijon, École d'ingénieur XYZ, etc.">
                    </div>
                    <?php endif; ?>

                    <input type="hidden" name="lieu" value="">
                    <input type="hidden" name="lien_candidature" value="">
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
                        <label for="pays">Pays*</label>
                        <input type="text" id="pays" name="pays" value="France"  readonly>
                    </div>

                    <div class="form-group">
                        <label for="region">Région*</label>
                        <select id="region" name="region"  onchange="updateDepartements()">
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
                        <label for="departement">Département*</label>
                        <select id="departement" name="departement"  onchange="updateVilles()">
                            <option value="">Sélectionner d'abord une région</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ville">Ville*</label>
                        <select id="ville" name="ville"  onchange="updateCodePostal()">
                            <option value="">Sélectionner d'abord un département</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code_postal">Code postal*</label>
                        <input type="text" id="code_postal" name="code_postal"  
                               pattern="[0-9]{5}" placeholder="Ex: 58000">
                    </div>

                    <input type="hidden" name="email_contact" value="<?= htmlspecialchars(getFormValue('email_contact')) ?>">
                    <input type="hidden" name="titre" value="<?= htmlspecialchars(getFormValue('titre')) ?>">
                    <input type="hidden" name="description" value="<?= htmlspecialchars(getFormValue('description')) ?>">
                    <input type="hidden" name="date_debut" value="<?= htmlspecialchars(getFormValue('date_debut')) ?>">
                    <input type="hidden" name="domaine" value="<?= htmlspecialchars(getFormValue('domaine')) ?>">
                    
                    <?php if ($_SESSION['form_data']['type_offre'] === 'alternance' && strpos(getFormValue('remuneration'), 'smic') !== false): ?>
                        <!-- Pour les alternances avec rémunération en SMIC -->
                        <input type="hidden" name="remuneration" value="<?= htmlspecialchars(getFormValue('remuneration')) ?>">
                    <?php elseif (getFormValue('remuneration') === 'autre'): ?>
                        <!-- Pour les "autre montant" -->
                        <input type="hidden" name="remuneration" value="<?= htmlspecialchars(getFormValue('remuneration_autre')) ?>">
                        <input type="hidden" name="remuneration_autre" value="<?= htmlspecialchars(getFormValue('remuneration_autre')) ?>">
                    <?php else: ?>
                        <!-- Pour les montants standard -->
                        <input type="hidden" name="remuneration" value="<?= htmlspecialchars(getFormValue('remuneration')) ?>">
                    <?php endif; ?>
                    
                    <input type="hidden" name="mode_stage" value="<?= htmlspecialchars(getFormValue('mode_stage')) ?>">
                    
                    <?php if($_SESSION['form_data']['type_offre'] === 'alternance'): ?>
                        <!-- Champs spécifiques à l'alternance -->
                        <input type="hidden" name="niveau_etude" value="<?= htmlspecialchars(getFormValue('niveau_etude')) ?>">
                        <input type="hidden" name="type_contrat" value="<?= htmlspecialchars(getFormValue('type_contrat')) ?>">
                        <input type="hidden" name="rythme_alternance" value="<?= htmlspecialchars(getFormValue('rythme_alternance')) ?>">
                        
                        <?php if(!empty(getFormValue('formation_visee'))): ?>
                            <input type="hidden" name="formation_visee" value="<?= htmlspecialchars(getFormValue('formation_visee')) ?>">
                        <?php endif; ?>
                        
                        <?php if(!empty(getFormValue('ecole_partenaire'))): ?>
                            <input type="hidden" name="ecole_partenaire" value="<?= htmlspecialchars(getFormValue('ecole_partenaire')) ?>">
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="form-navigation">
                        <button type="submit" name="prev_step" value="2">Précédent</button>
                        <button type="submit" name="final_submit">Publier l'offre</button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <a class="index-button" href="/Gestion_Stage/app/views/home.php">Retour à l'accueil</a>
    

    <script src="/Gestion_Stage/public/assets/js/location.js"></script>
    <script src="/Gestion_Stage/public/assets/js/remuneration.js"></script>
    <script>
       // Script pour gérer les options spécifiques à l'alternance
    function toggleAutreMontant() {
        const remunerationSelect = document.getElementById('remuneration');
        
        // Vérifier si l'élément existe avant d'accéder à ses propriétés
        if (remunerationSelect) {
            const autreMontantContainer = document.getElementById('autre_montant_container');
            
            if (remunerationSelect.value === 'autre') {
                autreMontantContainer.style.display = 'block';
            } else {
                autreMontantContainer.style.display = 'none';
            }
        }
    }
    
    // Initialiser l'affichage au chargement - uniquement si nous sommes dans l'étape 2
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si l'élément existe avant d'appeler la fonction
        if (document.getElementById('remuneration')) {
            toggleAutreMontant();
        }
    });

    function updateRemuneration(value) {
        // Cette fonction peut rester simple - elle est appelée uniquement 
        // quand l'utilisateur entre un montant manuel
        const input = document.getElementById('remuneration_autre');
        
        if (parseInt(value) < 417) {
            input.setCustomValidity('La rémunération minimale est de 417€');
        } else {
            input.setCustomValidity('');
        }
    }
    </script>
</body>
</html>

<?php
function formatRemuneration($remuneration, $type_offre, $type_remuneration = null) {
    if (empty($remuneration)) return 'Non spécifiée';
    
    // Si c'est une alternance avec un type de rémunération en SMIC
    if ($type_offre === 'alternance' && !empty($type_remuneration)) {
        switch ($type_remuneration) {
            case 'smic27': return '27% du SMIC (' . number_format($remuneration, 0, ',', ' ') . ' €)';
            case 'smic43': return '43% du SMIC (' . number_format($remuneration, 0, ',', ' ') . ' €)';
            case 'smic53': return '53% du SMIC (' . number_format($remuneration, 0, ',', ' ') . ' €)';
            case 'smic100': return '100% du SMIC (' . number_format($remuneration, 0, ',', ' ') . ' €)';
            default: return number_format($remuneration, 0, ',', ' ') . ' €/mois';
        }
    } else {
        // Format monétaire standard pour les stages et les autres cas
        return number_format($remuneration, 0, ',', ' ') . ' €/mois';
    }
}
?>