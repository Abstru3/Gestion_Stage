<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement du formulaire selon l'étape
    if (isset($_POST['final_submit'])) {
        // Traitement final du formulaire
        try {
            $stmt = $pdo->prepare("INSERT INTO offres_stages (
                entreprise_id, titre, description, email_contact, lien_candidature,
                date_debut, duree, domaine, remuneration, teletravail,
                pays, ville, code_postal, region, departement, lieu, mode_stage
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['titre'],
                $_POST['description'],
                $_POST['email_contact'],
                $_POST['lien_candidature'],
                $_POST['date_debut'],
                $_POST['duree'],
                $_POST['domaine'],
                $_POST['remuneration'],
                isset($_POST['teletravail']) ? 1 : 0,
                $_POST['pays'],
                $_POST['ville'],
                $_POST['code_postal'],
                $_POST['region'],
                $_POST['departement'],
                $_POST['lieu'],
                $_POST['mode_stage']
            ]);

            $success = "Offre de stage publiée avec succès!";
            header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de la publication: " . $e->getMessage();
        }
    }
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
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="multi-step-form" id="internshipForm">
            <?php if ($currentStep === 1): ?>
                <div class="step-content">
                    <h2>Étape 1 : Informations sur l'entreprise</h2>
                    
                    <div class="form-group">
                        <label for="nom_entreprise">Nom de l'entreprise*</label>
                        <input type="text" id="nom_entreprise" name="nom_entreprise" 
                               value="<?= htmlspecialchars($entreprise['nom']) ?>" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="email_contact">Email de contact*</label>
                        <input type="email" id="email_contact" name="email_contact" 
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
                        <button type="button" onclick="nextStep(2)">Suivant</button>
                    </div>
                </div>

            <?php elseif ($currentStep === 2): ?>
                <div class="step-content">
                    <h2>Étape 2 : Détails du stage</h2>
                    
                    <div class="form-group">
                        <label for="titre">Titre de l'offre*</label>
                        <input type="text" id="titre" name="titre" required maxlength="200"
                               placeholder="Ex: Assistant de recherche (6 mois)">
                    </div>

                    <div class="form-group">
                        <label for="description">Description du stage*</label>
                        <textarea id="description" name="description" required 
                                minlength="200" placeholder="Décrivez les missions, objectifs..."
                                oninvalid="this.setCustomValidity('La description doit contenir au moins 200 caractères')"
                                oninput="this.setCustomValidity('')"></textarea>
                        <small>Minimum 200 caractères requis</small>
                    </div>

                    <div class="form-group">
                        <label for="date_debut">Date de début*</label>
                        <input type="date" id="date_debut" name="date_debut" required
                               min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée du stage*</label>
                        <select id="duree" name="duree" required>
                            <option value="">Sélectionner une durée</option>
                            <option value="2 mois">2 mois</option>
                            <option value="3 mois">3 mois</option>
                            <option value="4 mois">4 mois</option>
                            <option value="5 mois">5 mois</option>
                            <option value="6 mois">6 mois</option>
                            <option value="12 mois">12 mois</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="domaine">Domaine*</label>
                        <select id="domaine" name="domaine" required>
                            <option value="">Sélectionner un domaine</option>
                            <optgroup label="Informatique">
                                <option value="developpement_web">Développement Web</option>
                                <option value="developpement_mobile">Développement Mobile</option>
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
                        <label for="remuneration">Rémunération mensuelle*</label>
                        <select id="remuneration" name="remuneration" required onchange="toggleAutreMontant()">
                            <option value="">Sélectionner une rémunération</option>
                            <option value="417">Minimum légal (417€)</option>
                            <option value="500">500€</option>
                            <option value="600">600€</option>
                            <option value="700">700€</option>
                            <option value="800">800€</option>
                            <option value="900">900€</option>
                            <option value="1000">1000€</option>
                            <option value="autre">Autre montant</option>
                        </select>
                        <div id="autre_montant_container" style="display: none; margin-top: 10px;">
                            <input type="number" 
                                   id="remuneration_autre" 
                                   name="remuneration_autre" 
                                   min="417" 
                                   step="1" 
                                   placeholder="Saisir un montant en euros"
                                   oninput="updateRemuneration(this.value)">
                            <small>Minimum légal : 417€</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="teletravail" value="1">
                            Télétravail possible
                        </label>
                    </div>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep(1)">Précédent</button>
                        <button type="button" onclick="nextStep(3)">Suivant</button>
                    </div>
                </div>

            <?php elseif ($currentStep === 3): ?>
                <div class="step-content">
                    <h2>Étape 3 : Localisation</h2>
                    
                    <div class="form-group">
                        <label for="pays">Pays*</label>
                        <input type="text" id="pays" name="pays" value="France" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="region">Région*</label>
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
                        <label for="departement">Département*</label>
                        <select id="departement" name="departement" required onchange="updateVilles()">
                            <option value="">Sélectionner d'abord une région</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ville">Ville*</label>
                        <select id="ville" name="ville" required onchange="updateCodePostal()">
                            <option value="">Sélectionner d'abord un département</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code_postal">Code postal*</label>
                        <input type="text" id="code_postal" name="code_postal" required 
                               pattern="[0-9]{5}" placeholder="Ex: 58000">
                    </div>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep(2)">Précédent</button>
                        <button type="submit" name="final_submit">Publier l'offre</button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function nextStep(step) {
            window.location.href = `?step=${step}`;
        }

        function prevStep(step) {
            window.location.href = `?step=${step}`;
        }
    </script>
    <script>
const regions = {
    "Bourgogne-Franche-Comté": {
        "58": "Nièvre",
        "21": "Côte-d'Or",
        "71": "Saône-et-Loire",
        "89": "Yonne",
        "25": "Doubs",
        "39": "Jura",
        "70": "Haute-Saône",
        "90": "Territoire de Belfort"
    },
    "Auvergne-Rhône-Alpes": {
        "03": "Allier",
        "63": "Puy-de-Dôme",
        "42": "Loire",
        "69": "Rhône"
    },
    "Île-de-France": {
        "75": "Paris",
        "77": "Seine-et-Marne",
        "78": "Yvelines",
        "91": "Essonne",
        "92": "Hauts-de-Seine",
        "93": "Seine-Saint-Denis",
        "94": "Val-de-Marne",
        "95": "Val-d'Oise"
    }
};

const villes = {
    "58": [
        "Nevers",
        "Cosne-Cours-sur-Loire",
        "Decize",
        "Fourchambault",
        "Varennes-Vauzelles",
        "La Charité-sur-Loire",
        "Imphy",
        "Clamecy",
        "Marzy",
        "Saint-Léger-des-Vignes"
    ],
    "21": ["Dijon", "Beaune", "Chenôve", "Talant"],
    "71": ["Mâcon", "Chalon-sur-Saône", "Autun", "Le Creusot"],
    "89": ["Auxerre", "Sens", "Joigny", "Avallon"]
};

function updateDepartements() {
    const regionSelect = document.getElementById('region');
    const departementSelect = document.getElementById('departement');
    const selectedRegion = regionSelect.value;
    
    departementSelect.innerHTML = '<option value="">Sélectionner un département</option>';
    
    if (selectedRegion && regions[selectedRegion]) {
        Object.entries(regions[selectedRegion]).forEach(([code, nom]) => {
            departementSelect.innerHTML += `<option value="${code}">${nom} (${code})</option>`;
        });
    }
    
    // Réinitialiser la sélection de ville
    document.getElementById('ville').innerHTML = '<option value="">Sélectionner d'abord un département</option>';
}

function updateVilles() {
    const departementSelect = document.getElementById('departement');
    const villeSelect = document.getElementById('ville');
    const selectedDepartement = departementSelect.value;
    
    villeSelect.innerHTML = '<option value="">Sélectionner une ville</option>';
    
    if (selectedDepartement && villes[selectedDepartement]) {
        villes[selectedDepartement].forEach(ville => {
            villeSelect.innerHTML += `<option value="${ville}">${ville}</option>`;
        });
    }

    // Mise à jour automatique du code postal pour Nevers
    const codePostalInput = document.getElementById('code_postal');
    if (selectedDepartement === "58" && villeSelect.value === "Nevers") {
        codePostalInput.value = "58000";
    }
}

// Écouter les changements de ville pour mettre à jour le code postal
document.getElementById('ville').addEventListener('change', function() {
    const codePostalInput = document.getElementById('code_postal');
    const departementSelect = document.getElementById('departement');
    
    if (departementSelect.value === "58") {
        switch(this.value) {
            case "Nevers":
                codePostalInput.value = "58000";
                break;
            case "Cosne-Cours-sur-Loire":
                codePostalInput.value = "58200";
                break;
            case "Decize":
                codePostalInput.value = "58300";
                break;
            case "Fourchambault":
                codePostalInput.value = "58600";
                break;
            case "Varennes-Vauzelles":
                codePostalInput.value = "58640";
                break;
            // etc.
        }
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regions = {
        "Bourgogne-Franche-Comté": {
            "58": "Nièvre",
            "21": "Côte-d'Or",
            "71": "Saône-et-Loire",
            "89": "Yonne",
            "25": "Doubs",
            "39": "Jura",
            "70": "Haute-Saône",
            "90": "Territoire de Belfort"
        },
        "Auvergne-Rhône-Alpes": {
            "03": "Allier",
            "63": "Puy-de-Dôme",
            "42": "Loire",
            "69": "Rhône"
        },
        "Île-de-France": {
            "75": "Paris",
            "77": "Seine-et-Marne",
            "78": "Yvelines",
            "91": "Essonne",
            "92": "Hauts-de-Seine",
            "93": "Seine-Saint-Denis",
            "94": "Val-de-Marne",
            "95": "Val-d'Oise"
        }
    };

    const villes = {
        "58": [
            "Nevers",
            "Cosne-Cours-sur-Loire",
            "Decize",
            "Fourchambault",
            "Varennes-Vauzelles",
            "La Charité-sur-Loire",
            "Imphy",
            "Clamecy",
            "Marzy",
            "Saint-Léger-des-Vignes"
        ],
        "21": ["Dijon", "Beaune", "Chenôve", "Talant"],
        "71": ["Mâcon", "Chalon-sur-Saône", "Autun", "Le Creusot"],
        "89": ["Auxerre", "Sens", "Joigny", "Avallon"]
    };

    const codesPostaux = {
        "Nevers": "58000",
        "Cosne-Cours-sur-Loire": "58200",
        "Decize": "58300",
        "Fourchambault": "58600",
        "Varennes-Vauzelles": "58640",
        "La Charité-sur-Loire": "58400",
        "Imphy": "58160",
        "Clamecy": "58500",
        "Marzy": "58180"
    };

    window.updateDepartements = function() {
        const regionSelect = document.getElementById('region');
        const departementSelect = document.getElementById('departement');
        const selectedRegion = regionSelect.value;
        
        departementSelect.innerHTML = '<option value="">Sélectionner un département</option>';
        
        if (selectedRegion && regions[selectedRegion]) {
            Object.entries(regions[selectedRegion]).forEach(([code, nom]) => {
                departementSelect.innerHTML += `<option value="${code}">${nom} (${code})</option>`;
            });
        }
        
        // Réinitialiser les champs dépendants
        document.getElementById('ville').innerHTML = '<option value="">Sélectionner d\'abord un département</option>';
        document.getElementById('code_postal').value = '';
    };

    window.updateVilles = function() {
        const departementSelect = document.getElementById('departement');
        const villeSelect = document.getElementById('ville');
        const selectedDepartement = departementSelect.value;
        
        villeSelect.innerHTML = '<option value="">Sélectionner une ville</option>';
        
        if (selectedDepartement && villes[selectedDepartement]) {
            villes[selectedDepartement].forEach(ville => {
                villeSelect.innerHTML += `<option value="${ville}">${ville}</option>`;
            });
        }
    };

    window.updateCodePostal = function() {
        const villeSelect = document.getElementById('ville');
        const codePostalInput = document.getElementById('code_postal');
        const selectedVille = villeSelect.value;
        
        if (codesPostaux[selectedVille]) {
            codePostalInput.value = codesPostaux[selectedVille];
        } else {
            codePostalInput.value = '';
        }
    };
});
</script>
<script>
function toggleAutreMontant() {
    const select = document.getElementById('remuneration');
    const container = document.getElementById('autre_montant_container');
    const input = document.getElementById('remuneration_autre');
    
    if (select.value === 'autre') {
        container.style.display = 'block';
        input.required = true;
        input.focus();
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
        // Réinitialiser les styles et messages d'erreur
        input.style.borderColor = '#ddd';
        const warningDiv = document.getElementById('remuneration_warning');
        if (warningDiv) warningDiv.textContent = '';
        document.querySelector('button[name="final_submit"]').disabled = false;
    }
}

function updateRemuneration(value) {
    const input = document.getElementById('remuneration_autre');
    const submitButton = document.querySelector('button[name="final_submit"]');
    const warningDiv = document.getElementById('remuneration_warning');
    
    // Créer le div d'avertissement s'il n'existe pas
    if (!warningDiv) {
        const warning = document.createElement('div');
        warning.id = 'remuneration_warning';
        warning.style.color = '#ff0000';
        warning.style.fontSize = '0.8em';
        warning.style.marginTop = '5px';
        input.parentNode.appendChild(warning);
    }

    if (value < 417) {
        document.getElementById('remuneration_warning').textContent = 
            'La rémunération ne peut pas être inférieure au minimum légal (417€)';
        submitButton.disabled = true;
        input.style.borderColor = '#ff0000';
    } else {
        document.getElementById('remuneration_warning').textContent = '';
        submitButton.disabled = false;
        input.style.borderColor = '#ddd';
    }
}

// Exécuter au chargement pour gérer les retours en arrière du navigateur
document.addEventListener('DOMContentLoaded', function() {
    toggleAutreMontant();
});
</script>
</body>
</html>