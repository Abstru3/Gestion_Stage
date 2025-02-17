<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Politique de confidentialité</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_policies.css">
    <link rel="icon" type="image/png" href="../assets/images/logo_reduis.png">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="policy-container">
        <div class="policy-header">
            <h1>Politique de confidentialité</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="policy-section">
            <h2>1. Collecte des données personnelles</h2>
            <p>Dans le cadre de votre utilisation de NeversStage, nous collectons les informations suivantes :</p>
            <ul class="policy-list">
                <li>Étudiants : nom, prénom, email, CV, lettre de motivation</li>
                <li>Entreprises : raison sociale, SIRET, coordonnées, description</li>
                <li>Données de connexion : identifiants, historique de connexion</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>2. Utilisation des données</h2>
            <p>Vos données sont utilisées pour :</p>
            <ul class="policy-list">
                <li>Gérer votre compte et profil utilisateur</li>
                <li>Faciliter la mise en relation étudiants-entreprises</li>
                <li>Traiter les candidatures aux offres de stage</li>
                <li>Assurer le suivi des stages</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Protection des données</h2>
            <p>Nous mettons en œuvre des mesures de sécurité pour protéger vos données :</p>
            <ul class="policy-list">
                <li>Chiffrement des mots de passe</li>
                <li>Accès sécurisé aux documents (CV, lettres)</li>
                <li>Hébergement sécurisé des données</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Durée de conservation</h2>
            <p>Les données sont conservées :</p>
            <ul class="policy-list">
                <li>Comptes actifs : pendant la durée d'utilisation du service</li>
                <li>Candidatures : 2 ans après la fin du stage</li>
                <li>Données de connexion : 1 an</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Vos droits</h2>
            <p>Conformément au RGPD, vous disposez des droits suivants :</p>
            <ul class="policy-list">
                <li>Accès à vos données personnelles</li>
                <li>Rectification des données inexactes</li>
                <li>Suppression de vos données</li>
                <li>Opposition au traitement</li>
                <li>Portabilité des données</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>6. Contact</h2>
            <p>Pour toute question concernant vos données personnelles :</p>
            <ul class="policy-list">
                <li>Email : privacy@neversstage.fr</li>
                <li>Adresse : IUT de Nevers, 58000 Nevers</li>
            </ul>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>