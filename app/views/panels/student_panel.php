<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

function formatStatus($status) {
    switch ($status) {
        case 'en_attente':
            return 'En attente';
        case 'accepte':
            return 'Acceptée';
        case 'refuse':
            return 'Refusée';
        default:
            return ucfirst($status);
    }
}

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

$query = "SELECT o.*, e.nom as entreprise_nom FROM offres_stages o 
          JOIN entreprises e ON o.entreprise_id = e.id 
          WHERE o.titre LIKE :search OR o.description LIKE :search OR e.nom LIKE :search 
          ORDER BY :sort $order";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':sort', $sort, PDO::PARAM_STR);
$stmt->execute();
$internships = $stmt->fetchAll();

$applications = get_applications($pdo, $_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Panneau Étudiant</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panneau Étudiant</h1>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><a href="/Gestion_Stage/app/views/home.php">🏠 Mon espace</a></li>
            <li><a href="/Gestion_Stage/app/views/profile.php">👤 Mon profil</a></li>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <main class="container">
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
        <?php if (empty($internships)): ?>
            <p>Aucune offre de stage ne correspond à votre recherche.</p>
        <?php else: ?>
            <?php foreach ($internships as $internship): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($internship['titre']); ?></h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Entreprise:</strong> <?php echo htmlspecialchars($internship['entreprise_nom']); ?></p>
                        <p><?php echo htmlspecialchars($internship['description']); ?></p>
                        <p>Date de début: <?php echo $internship['date_debut']; ?></p>
                        <p>Date de fin: <?php echo $internship['date_fin']; ?></p>
                    </div>
                    <div class="card-footer">
                        <form action="/Gestion_Stage/app/views/internships/apply.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="offre_id" value="<?php echo $internship['id']; ?>">
                            <label for="cv">CV (PDF) :</label>
                            <input type="file" name="cv" accept=".pdf" required>
                            <label for="lettre_motivation">Lettre de motivation (PDF) :</label>
                            <input type="file" name="lettre_motivation" accept=".pdf" required>
                            <button type="submit" class="btn-primary">Postuler</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Mes candidatures</h2>
        <?php if (empty($applications)): ?>
            <p>Vous n'avez pas encore postulé à des offres de stage.</p>
        <?php else: ?>
            <?php foreach ($applications as $application): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($application['titre']); ?></h3>
                    </div>
                    <div class="card-body">
                        <p>Statut: <?php echo formatStatus($application['statut']); ?></p>
                        <p>Date de candidature: <?php echo $application['date_candidature']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <p><a class="index-button" href="/Gestion_Stage/app/views/home.php">Retour à l'espace personnel</a></p>
</body>
</html>