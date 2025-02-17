<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Conditions d'utilisation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style_policies.css">
    <link rel="icon" type="image/png" href="../assets/images/logo_reduis.png">
</head>
<body>
    <?php include '../../app/includes/header.php'; ?>
    
    <div class="policy-container">
        <div class="policy-header">
            <h1>Conditions d'utilisation</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="policy-section">
            <h2>1. Acceptation des conditions</h2>
            <p>En accédant à NeversStage, vous acceptez d'être lié par ces conditions d'utilisation, toutes les lois et réglementations applicables.</p>
        </div>

        <div class="policy-section">
            <h2>2. Utilisation du service</h2>
            <p>Notre plateforme propose les services suivants :</p>
            <ul class="policy-list">
                <li>Publication d'offres de stages pour les entreprises</li>
                <li>Recherche de stages pour les étudiants</li>
                <li>Gestion des candidatures</li>
                <li>Système de messagerie interne</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Comptes utilisateurs</h2>
            <ul class="policy-list">
                <li>Vous êtes responsable de la confidentialité de votre compte</li>
                <li>Les informations fournies doivent être exactes et à jour</li>
                <li>L'utilisation frauduleuse sera sanctionnée</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Propriété intellectuelle</h2>
            <p>Tout le contenu publié sur NeversStage reste la propriété de leurs auteurs respectifs.</p>
        </div>

        <div class="policy-section">
            <h2>5. Modification des conditions</h2>
            <p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les utilisateurs seront informés des changements.</p>
        </div>
    </div>

    <?php include '../../app/includes/footer.php'; ?>
</body>
</html>