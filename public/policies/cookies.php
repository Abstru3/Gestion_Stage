<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Politique des cookies</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_policies.css">
    <link rel="icon" type="image/png" href="../assets/images/logo_reduis.png">
</head>
<body>
    <?php include '../../app/includes/header.php'; ?>
    
    <div class="policy-container">
        <div class="policy-header">
            <h1>Politique des cookies</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="policy-section">
            <h2>1. Qu'est-ce qu'un cookie ?</h2>
            <p>Un cookie est un petit fichier texte stocké sur votre appareil lors de la visite d'un site web.</p>
        </div>

        <div class="policy-section">
            <h2>2. Cookies utilisés</h2>
            <ul class="policy-list">
                <li>Cookies essentiels : pour le fonctionnement du site</li>
                <li>Cookies de session : pour gérer votre connexion</li>
                <li>Cookies de préférences : pour mémoriser vos choix</li>
                <li>Cookies analytiques : pour améliorer notre service</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Gestion des cookies</h2>
            <p>Vous pouvez gérer vos préférences en matière de cookies :</p>
            <ul class="policy-list">
                <li>Via les paramètres de votre navigateur</li>
                <li>Via notre panneau de gestion des cookies</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Durée de conservation</h2>
            <ul class="policy-list">
                <li>Cookies de session : supprimés à la fermeture du navigateur</li>
                <li>Cookies persistants : maximum 13 mois</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Plus d'informations</h2>
            <p>Pour toute question concernant l'utilisation des cookies :</p>
            <ul class="policy-list">
                <li>Email : privacy@neversstage.fr</li>
            </ul>
        </div>
    </div>

    <?php include '../../app/includes/footer.php'; ?>
</body>
</html>