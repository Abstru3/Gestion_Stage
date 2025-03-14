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

$entreprise_id = $_SESSION['user_id'];
$offre_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM offres_stages WHERE id = ? AND entreprise_id = ?");
$stmt->execute([$offre_id, $entreprise_id]);
$offre = $stmt->fetch();

if (!$offre) {
    header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
    exit();
}

// Déterminer le type d'offre (stage ou alternance)
$type_offre = $offre['type_offre'] ?? 'stage';

$stmt = $pdo->prepare("SELECT icone FROM entreprises WHERE id = ?");
$stmt->execute([$entreprise_id]);
$entreprise = $stmt->fetch();
$iconeEntreprise = $entreprise['icone'] ?? null;

$logo = !empty($offre['logo']) ? $offre['logo'] : (!empty($iconeEntreprise) ? $iconeEntreprise : 'uploads/logos/default.png');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $lien_candidature = $_POST['lien_candidature'] ?? null;
        $domaine = $_POST['domaine'] ?? null;
        $pays = $_POST['pays'] ?? 'France';
        $lieu = $_POST['lieu'] ?? null;

        $logoPath = $offre['logo'];

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
                    $logoPath = 'uploads/logos/' . $newFileName;
                } else {
                    throw new Exception("Erreur lors du téléchargement du logo.");
                }
            } else {
                throw new Exception("Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
            }
        }

        // Traitement de la rémunération
        $remuneration = $_POST['remuneration'];
        $type_remuneration = null;

        // Pour les alternances, récupérer le type de rémunération
        if ($type_offre === 'alternance' && strpos($_POST['remuneration'], 'smic') !== false) {
            $type_remuneration = $_POST['remuneration'];
            // Valeurs approximatives pour 2025
            switch ($type_remuneration) {
                case 'smic27': $remuneration = 486; break; // 27% de 1800€
                case 'smic43': $remuneration = 774; break; // 43% de 1800€
                case 'smic53': $remuneration = 954; break; // 53% de 1800€
                case 'smic100': $remuneration = 1800; break; // 100% de 1800€
            }
        } elseif ($_POST['remuneration'] === 'autre' && !empty($_POST['remuneration_autre'])) {
            $remuneration = $_POST['remuneration_autre'];
        }

        // Base SQL pour les champs communs
        $sql = "UPDATE offres_stages SET 
            titre = ?, 
            description = ?, 
            email_contact = ?, 
            lien_candidature = ?,
            date_debut = ?, 
            date_fin = ?,
            domaine = ?, 
            remuneration = ?,
            type_remuneration = ?,
            pays = ?, 
            ville = ?, 
            code_postal = ?, 
            region = ?, 
            departement = ?, 
            lieu = ?, 
            mode_stage = ?, 
            logo = ?";
        
        $params = [ 
            $_POST['titre'],
            $_POST['description'],
            $_POST['email_contact'],
            $lien_candidature,
            $_POST['date_debut'],
            $_POST['date_fin'],
            $domaine,
            $remuneration,
            $type_remuneration,
            $pays,
            $_POST['ville'],
            $_POST['code_postal'],
            $_POST['region'],
            $_POST['departement'],
            $lieu,
            $_POST['mode_stage'],
            $logoPath
        ];

        // Ajouter des champs spécifiques pour l'alternance
        if ($type_offre === 'alternance') {
            $sql .= ", niveau_etude = ?, duree_contrat = ?, type_contrat = ?, rythme_alternance = ?";
            if (isset($_POST['formation_visee'])) {
                $sql .= ", formation_visee = ?";
                $params[] = $_POST['formation_visee'];
            }
            if (isset($_POST['ecole_partenaire'])) {
                $sql .= ", ecole_partenaire = ?";
                $params[] = $_POST['ecole_partenaire'];
            }
            
            // Ajouter les paramètres pour les champs d'alternance
            array_splice($params, -0, 0, [
                $_POST['niveau_etude'] ?? null,
                $_POST['duree_contrat'] ?? null,
                $_POST['type_contrat'] ?? null,
                $_POST['rythme_alternance'] ?? null
            ]);
        }
        
        $sql .= " WHERE id = ? AND entreprise_id = ?";
        $params[] = $offre_id;
        $params[] = $entreprise_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success_message'] = $type_offre === 'alternance' ? 
            "L'offre d'alternance a été mise à jour avec succès!" : 
            "L'offre de stage a été mise à jour avec succès!";
        header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
        exit();
    } catch (Exception $e) {
        $error = "Erreur lors de la mise à jour: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Modifier une offre de <?= $type_offre === 'alternance' ? 'alternance' : 'stage' ?></title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_post_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body class="<?= $type_offre === 'alternance' ? 'alternance-mode' : '' ?>">
    <div class="container">
        <h1>Modifier l'offre <?= $type_offre === 'alternance' ? 'd\'alternance' : 'de stage' ?></h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="multi-step-form" id="internshipForm" enctype="multipart/form-data">
            <div class="step-content">
                <div class="form-group">
                    <label for="titre">Titre de l'offre*</label>
                    <input type="text" id="titre" name="titre" required maxlength="200"
                           value="<?= htmlspecialchars($offre['titre']) ?>"
                           placeholder="<?= $type_offre === 'alternance' ? 'Ex: Alternant développeur web' : 'Ex: Assistant de recherche (6 mois)' ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description <?= $type_offre === 'alternance' ? 'de l\'alternance' : 'du stage' ?>*</label>
                    <textarea id="description" name="description" required 
                            minlength="200" placeholder="Décrivez les missions, objectifs..."><?= htmlspecialchars($offre['description']) ?></textarea>
                    <small>Minimum 200 caractères requis</small>
                </div>

                <div class="form-group">
                    <label for="email_contact">Email de contact*</label>
                    <input type="email" id="email_contact" name="email_contact" 
                           value="<?= htmlspecialchars($offre['email_contact']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="date_debut">Date de début*</label>
                    <input type="date" id="date_debut" name="date_debut" 
                           value="<?= $offre['date_debut'] ?>"
                           required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin <?= $type_offre === 'alternance' ? '(estimée)' : '' ?></label>
                    <input type="date" id="date_fin" name="date_fin"
                           value="<?= $offre['date_fin'] ?? '' ?>"
                           min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="logo">Logo de l'entreprise</label>
                    <div class="logo-preview">
                        <?php if (!empty($offre['logo'])): ?>
                            <img src="/Gestion_Stage/public/<?= htmlspecialchars($offre['logo']); ?>" alt="Logo actuel" class="current-logo">
                        <?php endif; ?>
                    </div>
                    <input type="file" id="logo" name="logo" accept="image/*">
                    <p class="help-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</p>
                </div>

                <?php if($type_offre === 'alternance'): ?>
                <!-- Options de rémunération spécifiques à l'alternance -->
                <div class="form-group">
                    <label for="remuneration">Rémunération mensuelle*</label>
                    <select id="remuneration" name="remuneration" required onchange="toggleAutreMontant()">
                        <option value="">Sélectionner une rémunération</option>
                        <option value="smic27" <?= $offre['type_remuneration'] === 'smic27' ? 'selected' : '' ?>>27% du SMIC (moins de 18 ans)</option>
                        <option value="smic43" <?= $offre['type_remuneration'] === 'smic43' ? 'selected' : '' ?>>43% du SMIC (18-20 ans)</option>
                        <option value="smic53" <?= $offre['type_remuneration'] === 'smic53' ? 'selected' : '' ?>>53% du SMIC (21-25 ans)</option>
                        <option value="smic100" <?= $offre['type_remuneration'] === 'smic100' ? 'selected' : '' ?>>100% du SMIC (26 ans et plus)</option>
                        <option value="autre" <?= empty($offre['type_remuneration']) ? 'selected' : '' ?>>Autre montant</option>
                    </select>
                    <div id="autre_montant_container" style="display: <?= empty($offre['type_remuneration']) ? 'block' : 'none' ?>; margin-top: 10px;">
                        <input type="number" 
                               id="remuneration_autre" 
                               name="remuneration_autre" 
                               min="500" 
                               step="1" 
                               placeholder="Saisir un montant en euros"
                               value="<?= empty($offre['type_remuneration']) ? $offre['remuneration'] : '' ?>"
                               oninput="updateRemuneration(this.value)">
                        <small>Montant en euros</small>
                    </div>
                </div>
                <?php else: ?>
                <!-- Rémunération pour les stages -->
                <div class="form-group">
                    <label for="remuneration">Rémunération mensuelle*</label>
                    <select id="remuneration" name="remuneration" required onchange="toggleAutreMontant()">
                        <option value="">Sélectionner une rémunération</option>
                        <?php
                        $remunerations = [
                            "417" => "Minimum légal (417€)",
                            "500" => "500€",
                            "600" => "600€",
                            "700" => "700€",
                            "800" => "800€",
                            "900" => "900€",
                            "1000" => "1000€",
                            "autre" => "Autre montant"
                        ];
                        $selected_value = null;
                        foreach ($remunerations as $value => $label) {
                            // Si la valeur dans la base correspond à une des options prédéfinies
                            $selected = ($offre['remuneration'] == $value) ? 'selected' : '';
                            if ($selected) $selected_value = $value;
                            echo "<option value=\"$value\" $selected>$label</option>";
                        }
                        // Si aucune option n'a été sélectionnée, c'est "autre"
                        if (!$selected_value && $offre['remuneration']) {
                            echo "<script>document.addEventListener('DOMContentLoaded', function() { 
                                document.querySelector('#remuneration').value = 'autre';
                                document.querySelector('#autre_montant_container').style.display = 'block';
                            });</script>";
                        }
                        ?>
                    </select>
                    <div id="autre_montant_container" style="display: <?= ($selected_value === 'autre' || (!$selected_value && $offre['remuneration'])) ? 'block' : 'none' ?>; margin-top: 10px;">
                        <input type="number" 
                               id="remuneration_autre" 
                               name="remuneration_autre" 
                               min="417" 
                               step="1" 
                               placeholder="Saisir un montant en euros"
                               value="<?= (!in_array($offre['remuneration'], ['417', '500', '600', '700', '800', '900', '1000'])) ? $offre['remuneration'] : '' ?>"
                               oninput="updateRemuneration(this.value)">
                        <small>Minimum légal : 417€</small>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="mode_stage"><?= $type_offre === 'alternance' ? 'Mode de travail' : 'Mode de stage' ?>*</label>
                    <select id="mode_stage" name="mode_stage" required>
                        <option value="présentiel" <?= $offre['mode_stage'] === 'présentiel' ? 'selected' : '' ?>>Présentiel</option>
                        <option value="distanciel" <?= $offre['mode_stage'] === 'distanciel' ? 'selected' : '' ?>>Distanciel</option>
                        <option value="hybride" <?= $offre['mode_stage'] === 'hybride' ? 'selected' : '' ?>>Hybride</option>
                    </select>
                </div>

                <?php if($type_offre === 'alternance'): ?>
                <!-- Champs spécifiques à l'alternance -->
                <div class="form-group">
                    <label for="niveau_etude">Niveau d'étude requis*</label>
                    <select id="niveau_etude" name="niveau_etude" required>
                        <option value="">Sélectionner un niveau</option>
                        <option value="bac+2" <?= $offre['niveau_etude'] === 'bac+2' ? 'selected' : '' ?>>Bac+2</option>
                        <option value="bac+3" <?= $offre['niveau_etude'] === 'bac+3' ? 'selected' : '' ?>>Bac+3</option>
                        <option value="bac+5" <?= $offre['niveau_etude'] === 'bac+5' ? 'selected' : '' ?>>Bac+5</option>
                        <option value="autre" <?= $offre['niveau_etude'] === 'autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="duree_contrat">Durée du contrat d'alternance*</label>
                    <select id="duree_contrat" name="duree_contrat" required>
                        <option value="">Sélectionner une durée</option>
                        <option value="12" <?= $offre['duree_contrat'] === '12' ? 'selected' : '' ?>>12 mois</option>
                        <option value="24" <?= $offre['duree_contrat'] === '24' ? 'selected' : '' ?>>24 mois</option>
                        <option value="36" <?= $offre['duree_contrat'] === '36' ? 'selected' : '' ?>>36 mois</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="type_contrat">Type de contrat*</label>
                    <select id="type_contrat" name="type_contrat" required>
                        <option value="">Sélectionner un type de contrat</option>
                        <option value="apprentissage" <?= $offre['type_contrat'] === 'apprentissage' ? 'selected' : '' ?>>Contrat d'apprentissage</option>
                        <option value="professionnalisation" <?= $offre['type_contrat'] === 'professionnalisation' ? 'selected' : '' ?>>Contrat de professionnalisation</option>
                    </select>
                    <small>Le contrat d'apprentissage concerne les étudiants de moins de 30 ans</small>
                </div>

                <div class="form-group">
                    <label for="rythme_alternance">Rythme d'alternance*</label>
                    <select id="rythme_alternance" name="rythme_alternance" required>
                        <option value="">Sélectionner un rythme</option>
                        <option value="1sem_1sem" <?= $offre['rythme_alternance'] === '1sem_1sem' ? 'selected' : '' ?>>1 semaine entreprise / 1 semaine école</option>
                        <option value="2sem_1sem" <?= $offre['rythme_alternance'] === '2sem_1sem' ? 'selected' : '' ?>>2 semaines entreprise / 1 semaine école</option>
                        <option value="3sem_1sem" <?= $offre['rythme_alternance'] === '3sem_1sem' ? 'selected' : '' ?>>3 semaines entreprise / 1 semaine école</option>
                        <option value="1mois_1sem" <?= $offre['rythme_alternance'] === '1mois_1sem' ? 'selected' : '' ?>>1 mois entreprise / 1 semaine école</option>
                        <option value="autre" <?= $offre['rythme_alternance'] === 'autre' ? 'selected' : '' ?>>Autre rythme</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="formation_visee">Formation visée</label>
                    <input type="text" id="formation_visee" name="formation_visee"
                           value="<?= htmlspecialchars($offre['formation_visee'] ?? '') ?>"
                           placeholder="Ex: BTS SIO, Licence Pro développement web, etc.">
                </div>

                <div class="form-group">
                    <label for="ecole_partenaire">École partenaire (optionnel)</label>
                    <input type="text" id="ecole_partenaire" name="ecole_partenaire"
                           value="<?= htmlspecialchars($offre['ecole_partenaire'] ?? '') ?>"
                           placeholder="Ex: IUT de Dijon, École d'ingénieur XYZ, etc.">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="region">Région*</label>
                    <select id="region" name="region" required onchange="updateDepartements()">
                    </select>
                </div>

                <div class="form-group">
                    <label for="departement">Département*</label>
                    <select id="departement" name="departement" required onchange="updateVilles()">
                    </select>
                </div>

                <div class="form-group">
                    <label for="ville">Ville*</label>
                    <select id="ville" name="ville" required onchange="updateCodePostal()">
                    </select>
                </div>

                <div class="form-group">
                    <label for="code_postal">Code postal*</label>
                    <input type="text" id="code_postal" name="code_postal" 
                           value="<?= htmlspecialchars($offre['code_postal']) ?>"
                           required pattern="[0-9]{5}" placeholder="Ex: 58000">
                </div>

                <div class="form-group">
                    <label for="domaine">Domaine*</label>
                    <select id="domaine" name="domaine" required>
                        <option value="">Sélectionner un domaine</option>
                        <optgroup label="Informatique">
                            <option value="developpement_web" <?= ($offre['domaine'] ?? '') === 'developpement_web' ? 'selected' : '' ?>>Développement Web</option>
                            <option value="developpement_mobile" <?= ($offre['domaine'] ?? '') === 'developpement_mobile' ? 'selected' : '' ?>>Développement Mobile</option>
                            <option value="reseaux" <?= ($offre['domaine'] ?? '') === 'reseaux' ? 'selected' : '' ?>>Réseaux</option>
                            <option value="cybersecurite" <?= ($offre['domaine'] ?? '') === 'cybersecurite' ? 'selected' : '' ?>>Cybersécurité</option>
                        </optgroup>
                        <optgroup label="Commerce">
                            <option value="marketing_digital" <?= ($offre['domaine'] ?? '') === 'marketing_digital' ? 'selected' : '' ?>>Marketing Digital</option>
                            <option value="commerce_international" <?= ($offre['domaine'] ?? '') === 'commerce_international' ? 'selected' : '' ?>>Commerce International</option>
                            <option value="vente" <?= ($offre['domaine'] ?? '') === 'vente' ? 'selected' : '' ?>>Vente</option>
                        </optgroup>
                        <optgroup label="Autres">
                            <option value="finance" <?= ($offre['domaine'] ?? '') === 'finance' ? 'selected' : '' ?>>Finance</option>
                            <option value="ressources_humaines" <?= ($offre['domaine'] ?? '') === 'ressources_humaines' ? 'selected' : '' ?>>Ressources Humaines</option>
                            <option value="communication" <?= ($offre['domaine'] ?? '') === 'communication' ? 'selected' : '' ?>>Communication</option>
                            <option value="logistique" <?= ($offre['domaine'] ?? '') === 'logistique' ? 'selected' : '' ?>>Logistique</option>
                            <option value="autre" <?= ($offre['domaine'] ?? '') === 'autre' ? 'selected' : '' ?>>Autre</option>
                        </optgroup>
                    </select>
                </div>

                <input type="hidden" name="lien_candidature" value="<?= htmlspecialchars($offre['lien_candidature'] ?? '') ?>">
                <input type="hidden" name="pays" value="<?= htmlspecialchars($offre['pays'] ?? 'France') ?>">
                <input type="hidden" name="lieu" value="<?= htmlspecialchars($offre['lieu'] ?? '') ?>">

                <div class="form-navigation">
                    <button type="submit" name="submit" class="btn-primary">Mettre à jour l'offre</button>
                </div>
            </div>
        </form>
        
    </div>
    <div class="return-button-container">
            <a class="index-button" href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a>
        </div>

    <script>
        const currentRegion = "<?= htmlspecialchars($offre['region']) ?>";
        const currentDepartement = "<?= htmlspecialchars($offre['departement']) ?>";
        const currentVille = "<?= htmlspecialchars($offre['ville']) ?>";
        
        // Initialiser l'affichage "autre montant" si nécessaire
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('remuneration')) {
                toggleAutreMontant();
            }
        });
    </script>
    <script src="/Gestion_Stage/public/assets/js/location.js"></script>
    <script src="/Gestion_Stage/public/assets/js/remuneration.js"></script>
</body>
</html>

