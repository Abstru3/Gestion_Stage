<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_debut';
$order = $_GET['order'] ?? 'ASC';

// Vérification de la validité du tri et de l'ordre
$valid_sorts = ['date_debut', 'titre', 'entreprise_nom'];
$valid_orders = ['ASC', 'DESC'];

if (!in_array($sort, $valid_sorts)) {
    $sort = 'date_debut';
}

if (!in_array($order, $valid_orders)) {
    $order = 'ASC';
}

// Récupérer toutes les offres de stage
$query = "SELECT o.*, e.nom as entreprise_nom 
          FROM offres_stages o 
          JOIN entreprises e ON o.entreprise_id = e.id 
          WHERE o.titre LIKE :search OR o.description LIKE :search OR e.nom LIKE :search 
          ORDER BY $sort $order";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$internships = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Toutes les offres de stage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_all_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <header>
        <div class="container-all-internships">
            <h1>Toutes les offres de stage</h1>
        </div>
    </header>

    <main class="container-all-internships">
        <h2>Rechercher des offres de stages</h2>
        <form action="" method="get">
            <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="sort">
                <option value="date_debut" <?php echo $sort == 'date_debut' ? 'selected' : ''; ?>>Date de début</option>
                <option value="titre" <?php echo $sort == 'titre' ? 'selected' : ''; ?>>Titre</option>
                <option value="entreprise_nom" <?php echo $sort == 'entreprise_nom' ? 'selected' : ''; ?>>Entreprise</option>
            </select>
            <select name="order">
                <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Croissant</option>
                <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Décroissant</option>
            </select>
            <button type="submit" class="btn-primary">Rechercher</button>
        </form>

        <h2>Offres de stages disponibles</h2>
        <div class="offers-grid">
            <?php if (empty($internships)): ?>
                <p>Aucune offre de stage ne correspond à votre recherche.</p>
            <?php else: ?>
                <?php foreach ($internships as $internship): ?>
                    <div class="offer-card">
                        <div class="offer-header">
                            <div class="offer-header-content">
                                <div>
                                    <h3><?php echo htmlspecialchars($internship['titre']); ?></h3>
                                    <span class="company-name">
                                        <?php echo htmlspecialchars($internship['entreprise_nom'] ?? 'Entreprise inconnue'); ?>
                                    </span>
                                </div>
                                <?php if (!empty($internship['logo'])): ?>
                                    <!-- <img src="/Gestion_Stage/<?php echo htmlspecialchars($internship['logo']); ?>" alt="Logo de l'entreprise" class="company-logo"> -->
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="offer-body">
                            <p><?php echo htmlspecialchars(substr($internship['description'], 0, 100)) . '...'; ?></p>
                            <div class="offer-details">
                                <span><i class="fas fa-calendar-alt"></i> Début: <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> Lieu: <?php echo htmlspecialchars($internship['lieu']); ?></span>
                                <span><i class="fas fa-globe"></i> Mode: <?php echo htmlspecialchars($internship['mode_stage']); ?></span>
                            </div>
                        </div>
                        <div class="offer-footer">
                            <a href="/Gestion_Stage/app/views/internships/stage_details.php?id=<?php echo $internship['id']; ?>" class="btn btn-details">Voir plus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <a href="/Gestion_Stage/index.php" class="btn index-button"><i class="fas fa-arrow-left"></i> Retour au menu</a>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const offers = document.querySelectorAll('.offer-card');
            offers.forEach((offer, index) => {
                setTimeout(() => {
                    offer.classList.add('visible');
                }, index * 200);
            });
        });
    </script>
</body>
</html>