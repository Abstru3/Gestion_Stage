<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

try {
    $feedbacks = $pdo->query("
        SELECT feedback, user_name, role, date
        FROM feedback
        ORDER BY RAND()
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de l'exécution de la requête : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - À propos</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_a_propos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
    <script src="/Gestion_Stage/public/assets/js/a_propos.js" defer></script>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>À propos de</h1>
            <img src="/Gestion_Stage/public/assets/images/logo.png" alt="NeversStage" height="75" class="fade-in">
        </div>
    </header>

    <section class="about-section">
        <div class="about-content">
            <h2>Notre mission</h2>
            <p>Chez <strong>NeversStage</strong>, notre mission est de faciliter la communication entre étudiants et entreprises, ainsi que d'offrir une gestion simple des offres de stage et alternance.</p>
            <h2>Qui sommes-nous ?</h2>
            <p>Nous sommes une équipe d'étudiants en IUT Informatique. NeversStage est le fruit de notre projet de créer une plateforme de gestion de stage simple d'utilisation.</p>
            <h2>Contact</h2>
            <p>Pour toute question ou suggestion, n'hésitez pas à nous contacter à <a href="mailto:contact@nevers_stage.fr">contact@nevers_stage.fr</a>.</p>
        </div>
    </section>

    <section class="team-section">
        <h2>Notre équipe</h2>
        <div class="team-members">
            <div class="member">
                <img src="../../public/assets/images/a_propos/leo_henriot.jpg" alt="Photo de Léo Henriot">
                <h3>Léo Henriot</h3>
                <p id="role">Rôle de ...</p>
                <p>Répartition des tâches.</p>
            </div>
            <div class="member">
                <img src="../../public/assets/images/a_propos/besjan_koraqi.jpg" alt="Photo de Bejan Koraqi">
                <h3>Besjan Koraqi</h3>
                <p id="role">Rôle de ...</p>
                <p>Répartition des tâches.</p>
            </div>
        </div>
    </section>

    <section class="timeline-section">
        <h2>Notre histoire</h2>
        <div class="timeline">
            <div class="event">
                <h3>27 Janvier 2025 - Lancement de NeversStage</h3>
                <p>NeversStage a commencé en Janvier en tant que projet de Stage de deuxième année BUT Informatique.</p>
            </div>
            <div class="event">
                <h3>Février à Mars 2025 - Grande avancée sur le projet</h3>
                <p>Le projet a vu une grande avancée durant les deux mois de stage.</p>
            </div>
            <div class="event">
                <h3>23 Mars 2025 - Rendu du projet</h3>
                <p>Le projet complet est livré à cette période, marquant la fin de celui-ci.</p>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <h2>Témoignages</h2>
        <div class="testimonials">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="testimonial">
                    <p>"<?php echo htmlspecialchars($feedback['feedback']); ?>" - <?php echo htmlspecialchars($feedback['user_name']); ?>, <?php echo htmlspecialchars($feedback['role']); ?> (<?php echo date('d/m/Y', strtotime($feedback['date'])); ?>)</p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a>
</body>
</html>