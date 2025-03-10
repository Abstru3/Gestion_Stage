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

        $stmt = $pdo->prepare("UPDATE offres_stages SET 
            titre = ?, 
            description = ?, 
            email_contact = ?, 
            lien_candidature = ?,
            date_debut = ?, 
            date_fin = ?,
            domaine = ?, 
            remuneration = ?,
            pays = ?, 
            ville = ?, 
            code_postal = ?, 
            region = ?, 
            departement = ?, 
            lieu = ?, 
            mode_stage = ?, 
            logo = ?
            WHERE id = ? AND entreprise_id = ?");

        $stmt->execute([ 
            $_POST['titre'],
            $_POST['description'],
            $_POST['email_contact'],
            $lien_candidature,
            $_POST['date_debut'],
            $_POST['date_fin'],
            $domaine,
            $_POST['remuneration'],
            $pays,
            $_POST['ville'],
            $_POST['code_postal'],
            $_POST['region'],
            $_POST['departement'],
            $lieu,
            $_POST['mode_stage'],
            $logoPath,
            $offre_id,
            $entreprise_id
        ]);

        $_SESSION['success_message'] = "L'offre a été mise à jour avec succès!";
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
    <title>NeversStage - Modifier une offre de stage</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_post_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <h1>Modifier l'offre de stage</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="multi-step-form" id="internshipForm" enctype="multipart/form-data">
            <div class="step-content">
                <div class="form-group">
                    <label for="titre">Titre de l'offre*</label>
                    <input type="text" id="titre" name="titre" required maxlength="200"
                           value="<?= htmlspecialchars($offre['titre']) ?>"
                           placeholder="Ex: Assistant de recherche (6 mois)">
                </div>

                <div class="form-group">
                    <label for="description">Description du stage*</label>
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
                        <label for="date_fin">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin"
                               value="<?= htmlspecialchars(getFormValue('date_fin')) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo de l'entreprise</label>
                        <div class="logo-preview">
                            <?php if (!empty($offre['logo'])): ?>
                                <img src="/Gestion_Stage/public/uploads/logos/<?php echo htmlspecialchars($offre['logo']); ?>" alt="Logo actuel" class="current-logo">
                            <?php endif; ?>
                        </div>
                        <input type="file" id="logo" name="logo" accept="image/*">
                        <p class="help-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</p>
                    </div>


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
                        foreach ($remunerations as $value => $label) {
                            $selected = ($offre['remuneration'] == $value) ? 'selected' : '';
                            echo "<option value=\"$value\" $selected>$label</option>";
                        }
                        ?>
                    </select>
                    <div id="autre_montant_container" style="display: none; margin-top: 10px;">
                        <input type="number" 
                               id="remuneration_autre" 
                               name="remuneration_autre" 
                               min="417" 
                               step="1" 
                               placeholder="Saisir un montant en euros"
                               value="<?= $offre['remuneration'] ?>"
                               oninput="updateRemuneration(this.value)">
                        <small>Minimum légal : 417€</small>
                    </div>
                </div>


                <div class="form-group">
                    <label for="mode_stage">Mode de stage*</label>
                    <select id="mode_stage" name="mode_stage" required>
                        <option value="présentiel" <?= $offre['mode_stage'] === 'présentiel' ? 'selected' : '' ?>>Présentiel</option>
                        <option value="distanciel" <?= $offre['mode_stage'] === 'distanciel' ? 'selected' : '' ?>>Distanciel</option>
                    </select>
                </div>

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
        
        <div class="return-button-container">
            <a class="index-button" href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a>
        </div>
    </div>

    <script>
        const currentRegion = "<?= htmlspecialchars($offre['region']) ?>";
        const currentDepartement = "<?= htmlspecialchars($offre['departement']) ?>";
        const currentVille = "<?= htmlspecialchars($offre['ville']) ?>";
    </script>
    <script src="/Gestion_Stage/public/assets/js/location.js"></script>
    <script src="/Gestion_Stage/public/assets/js/remuneration.js"></script>
</body>
</html>

