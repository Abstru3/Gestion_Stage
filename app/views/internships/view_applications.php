<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$offre_id = $_GET['offre_id'] ?? 0;

$statusFilter = $_GET['status'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'date_desc';

$stmt = $pdo->prepare("SELECT titre, description, type_offre FROM offres_stages WHERE id = ?");
$stmt->execute([$offre_id]);
$offre = $stmt->fetch();

$sql = "SELECT c.id, c.statut, c.lettre_motivation, c.date_candidature, e.nom, e.prenom, e.email, e.cv as cv_etudiant 
        FROM candidatures c 
        JOIN etudiants e ON c.etudiant_id = e.id 
        WHERE c.offre_id = :offre_id";

if ($statusFilter !== 'all') {
    $sql .= " AND c.statut = :status";
}

switch ($sortBy) {
    case 'date_asc':
        $sql .= " ORDER BY c.date_candidature ASC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY e.nom ASC, e.prenom ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY e.nom DESC, e.prenom DESC";
        break;
    case 'date_desc':
    default:
        $sql .= " ORDER BY c.date_candidature DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':offre_id', $offre_id);

if ($statusFilter !== 'all') {
    $stmt->bindParam(':status', $statusFilter);
}

$stmt->execute();
$applications = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidature_id = $_POST['candidature_id'];
    $action = $_POST['action'];

    if ($action == 'accepter') {
        $statut = 'acceptee';
    } elseif ($action == 'refuser') {
        $statut = 'refusee';
    }

    $stmt = $pdo->prepare("UPDATE candidatures SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $candidature_id]);

    header("Location: view_applications.php?offre_id=$offre_id&status=$statusFilter&sort=$sortBy");
    exit();
}

function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y à H:i', $timestamp);
}

function getInitials($name) {
    $nameParts = explode(' ', $name);
    $initials = '';
    foreach ($nameParts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return substr($initials, 0, 2);
}

$stats = [
    'total' => count($applications),
    'en_attente' => 0,
    'acceptee' => 0,
    'refusee' => 0
];

$stmt = $pdo->prepare("SELECT statut, COUNT(*) as count FROM candidatures WHERE offre_id = ? GROUP BY statut");
$stmt->execute([$offre_id]);
$statResults = $stmt->fetchAll();

foreach ($statResults as $statResult) {
    $stats[$statResult['statut']] = $statResult['count'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatures - <?= htmlspecialchars($offre['titre'] ?? 'Offre non trouvée') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_view_applications.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <header>
            <div class="breadcrumb">
                <a href="/Gestion_Stage/app/views/panels/company_panel.php"><i class="fas fa-home"></i> Tableau de bord</a>
                <i class="fas fa-chevron-right"></i>
                <span>Candidatures</span>
            </div>
            <h1 class="page-title">Gestion des candidatures</h1>
        </header>

        <?php if ($offre): ?>
            <div class="job-info">
                <h2 class="job-title">
                    <?= htmlspecialchars($offre['titre']) ?>
                    <?php if ($offre['type_offre'] === 'alternance'): ?>
                        <span class="status-badge" style="background-color: rgba(255, 140, 0, 0.15); color: darkorange;">Alternance</span>
                    <?php else: ?>
                        <span class="status-badge" style="background-color: rgba(44, 123, 229, 0.15); color: var(--secondary-color);">Stage</span>
                    <?php endif; ?>
                </h2>
                <p class="job-description" id="job-description"><?= htmlspecialchars($offre['description']) ?></p>
                <button class="expand-btn" id="expand-btn">Afficher plus</button>
            </div>

            <!-- Dashboard de statistiques -->
            <div class="stats-dashboard">
                <div class="stat-card">
                    <div class="stat-icon all"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['total'] ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['en_attente'] ?? 0 ?></div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon accepted"><i class="fas fa-check"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['acceptee'] ?? 0 ?></div>
                        <div class="stat-label">Acceptées</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rejected"><i class="fas fa-times"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['refusee'] ?? 0 ?></div>
                        <div class="stat-label">Refusées</div>
                    </div>
                </div>
            </div>

            <!-- Filtres et tri -->
            <div class="filters-toolbar">
                <div class="filter-group">
                    <label for="status-filter">Filtrer par statut:</label>
                    <select id="status-filter" class="filter-select" onchange="applyFilters()">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Tous les statuts</option>
                        <option value="en_attente" <?= $statusFilter === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="acceptee" <?= $statusFilter === 'acceptee' ? 'selected' : '' ?>>Acceptées</option>
                        <option value="refusee" <?= $statusFilter === 'refusee' ? 'selected' : '' ?>>Refusées</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort-by">Trier par:</label>
                    <select id="sort-by" class="filter-select" onchange="applyFilters()">
                        <option value="date_desc" <?= $sortBy === 'date_desc' ? 'selected' : '' ?>>Plus récentes</option>
                        <option value="date_asc" <?= $sortBy === 'date_asc' ? 'selected' : '' ?>>Plus anciennes</option>
                        <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Nom (A-Z)</option>
                        <option value="name_desc" <?= $sortBy === 'name_desc' ? 'selected' : '' ?>>Nom (Z-A)</option>
                    </select>
                </div>
                
                <button id="clear-filters" class="clear-filters" onclick="clearFilters()">
                    <i class="fas fa-sync-alt"></i> Réinitialiser
                </button>
            </div>

            <?php if (empty($applications)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3 class="empty-state-title">Aucune candidature <?= $statusFilter !== 'all' ? 'avec ce statut' : 'reçue' ?></h3>
                    <p class="empty-state-text">
                        <?= $statusFilter !== 'all' ? 'Essayez de modifier vos filtres pour voir plus de candidatures.' : 'Lorsque des étudiants postuleront à cette offre, leurs candidatures apparaîtront ici.' ?>
                    </p>
                    <?php if ($statusFilter !== 'all'): ?>
                        <button class="btn-secondary mt-3" onclick="clearFilters()">Voir toutes les candidatures</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="results-summary">
                    <?= count($applications) ?> candidature(s) <?= $statusFilter !== 'all' ? 'avec le statut "' . ($statusFilter === 'en_attente' ? 'En attente' : ($statusFilter === 'acceptee' ? 'Acceptée' : 'Refusée')) . '"' : '' ?>
                </div>
                
                <div class="applications-grid">
                    <?php foreach ($applications as $application): ?>
                        <?php
                            $fullName = $application['prenom'] . ' ' . $application['nom'];
                            $initials = getInitials($fullName);
                            $date = isset($application['date_candidature']) ? formatDate($application['date_candidature']) : 'Date inconnue';
                            
                            $statusClass = '';
                            $statusIcon = '';
                            $statusText = '';
                            switch ($application['statut']) {
                                case 'en_attente':
                                    $statusClass = 'pending';
                                    $statusIcon = 'fa-clock';
                                    $statusText = 'En attente';
                                    break;
                                case 'acceptee':
                                    $statusClass = 'accepted';
                                    $statusIcon = 'fa-check';
                                    $statusText = 'Acceptée';
                                    break;
                                case 'refusee':
                                    $statusClass = 'rejected';
                                    $statusIcon = 'fa-times';
                                    $statusText = 'Refusée';
                                    break;
                            }
                        ?>
                        <div class="application-card">
                            <div class="card-header">
                                <div class="applicant-info">
                                    <div class="applicant-avatar"><?= $initials ?></div>
                                    <div>
                                        <h3 class="applicant-name"><?= htmlspecialchars($fullName) ?></h3>
                                        <p class="applicant-email"><?= htmlspecialchars($application['email']) ?></p>
                                    </div>
                                </div>
                                <span class="status-badge <?= $statusClass ?>">
                                    <i class="fas <?= $statusIcon ?>"></i> <?= $statusText ?>
                                </span>
                            </div>
                            
                            <div class="card-body">
                                <div class="detail-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="detail-label">Candidature reçue le:</span>
                                    <span class="detail-value"><?= $date ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="detail-label">Documents:</span>
                                    <div class="document-links">
                                        <?php if (!empty($application['cv_etudiant'])): ?>
                                            <a href="/Gestion_Stage/public/uploads/cv/<?= htmlspecialchars($application['cv_etudiant']) ?>" 
                                               target="_blank" class="document-link">
                                                <i class="far fa-file-pdf"></i> CV
                                            </a>
                                        <?php else: ?>
                                            <span class="detail-value">CV non fourni</span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($application['lettre_motivation'])): ?>
                                            <a href="/Gestion_Stage/public/uploads/candidatures/<?= htmlspecialchars($application['lettre_motivation']) ?>" 
                                               target="_blank" class="document-link">
                                                <i class="far fa-file-alt"></i> Lettre
                                            </a>
                                        <?php else: ?>
                                            <span class="detail-value">Lettre non fournie</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($application['statut'] == 'en_attente'): ?>
                                <div class="card-footer">
                                    <form action="" method="post">
                                        <input type="hidden" name="candidature_id" value="<?= $application['id'] ?>">
                                        <button type="submit" name="action" value="refuser" class="btn btn-reject">
                                            <i class="fas fa-times"></i> Refuser
                                        </button>
                                        <button type="submit" name="action" value="accepter" class="btn btn-accept">
                                            <i class="fas fa-check"></i> Accepter
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3 class="empty-state-title">Offre non trouvée</h3>
                <p class="empty-state-text">L'offre demandée n'existe pas ou a été supprimée.</p>
            </div>
        <?php endif; ?>
        
        <a href="/Gestion_Stage/app/views/panels/company_panel.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const description = document.getElementById('job-description');
            const expandBtn = document.getElementById('expand-btn');
            
            if (description && expandBtn) {
                expandBtn.addEventListener('click', function() {
                    if (description.classList.contains('expanded')) {
                        description.classList.remove('expanded');
                        expandBtn.textContent = 'Afficher plus';
                    } else {
                        description.classList.add('expanded');
                        expandBtn.textContent = 'Afficher moins';
                    }
                });
            }
        });
        
        function applyFilters() {
            const statusFilter = document.getElementById('status-filter').value;
            const sortBy = document.getElementById('sort-by').value;
            const currentUrl = new URL(window.location.href);
            
            currentUrl.searchParams.set('status', statusFilter);
            currentUrl.searchParams.set('sort', sortBy);
            
            window.location.href = currentUrl.toString();
        }
        
        function clearFilters() {
            const currentUrl = new URL(window.location.href);
            
            const offreId = currentUrl.searchParams.get('offre_id');
            currentUrl.search = '';
            currentUrl.searchParams.set('offre_id', offreId);
            
            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>