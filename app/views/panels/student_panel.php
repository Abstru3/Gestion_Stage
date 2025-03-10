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
            return '<span class="status status-pending"><i class="fas fa-clock"></i> En attente</span>';
        case 'acceptee':
            return '<span class="status status-accepted"><i class="fas fa-check-circle"></i> Acceptée</span>';
        case 'refusee':
            return '<span class="status status-rejected"><i class="fas fa-times-circle"></i> Refusée</span>';
        default:
            return '<span class="status status-pending"><i class="fas fa-clock"></i> En attente</span>';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_student_panel.css">
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
            <li><a href="/Gestion_Stage/app/views/profile.php">👤 Mon profil</a></li>
            <?php if ($_SESSION['role'] == 'etudiant'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/student_panel.php">🔍 Offres de stages</a></li>
                <li><a href="/Gestion_Stage/app/message/inbox.php">📩 Mes Messages</a></li>
            <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
                <li><a href="/Gestion_Stage/app/views/internships/post_internship.php">➕ Publier une offre</a></li>
                <li><a href="/Gestion_Stage/app/views/panels/company_panel.php">📋 Gérer candidatures</a></li>
                <!--- <li><a href="/Gestion_Stage/app/message/inbox.php">📩 Mes Messages</a></li> --->
            <?php elseif ($_SESSION['role'] == 'admin'): ?>
                <li><a href="/Gestion_Stage/app/views/panels/admin_panel.php">🛠️ Panel Admin</a></li>
            <?php endif; ?>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-search"></i> Accédez à toutes les offres</h2>
            </div>
            <div class="card-body text-center">
                <p>Découvrez l'ensemble des offres de stage disponibles et filtrez selon vos critères.</p>
                <a href="/Gestion_Stage/app/views/internships/all_internships.php" class="btn-large">
                    <i class="fas fa-list"></i> Voir toutes les offres
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-file-alt"></i> Mes candidatures</h2>
            </div>
            <div class="card-body text-center applications">
                <?php if (empty($applications)): ?>
                    <p>Vous n'avez pas encore postulé à des offres de stage.</p>
                <?php else: ?>
                    <?php foreach ($applications as $application): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($application['titre']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><?php echo formatStatus($application['statut']); ?></p>
                                <p><i class="fas fa-calendar"></i> Date de candidature: <?php echo date('d/m/Y', strtotime($application['date_candidature'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <p><a class="index-button" href="/Gestion_Stage/app/views/home.php"><i class="fas fa-arrow-left"></i> Retour à l'espace personnel</a></p>
</body>
</html>