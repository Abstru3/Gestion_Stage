<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    error_log("User not logged in, redirecting to login page");
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

// Fonction pour formater les statuts
function formatStatus($status) {
    switch ($status) {
        case 'en_attente':
            return 'En attente';
        case 'acceptee':
            return 'Acceptée';
        case 'refusee':
            return 'Refusée';
        default:
            return ucfirst($status);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$table = ($_SESSION['role'] == 'etudiant') ? 'etudiants' : 'entreprises';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$welcome_message = 'Utilisateur';
if ($_SESSION['role'] == 'etudiant') {
    $welcome_message = $user['nom'] . ' ' . $user['prenom'];
} elseif ($_SESSION['role'] == 'entreprise') {
    $welcome_message = $user['nom'];
} elseif ($_SESSION['role'] == 'admin') {
    $welcome_message = 'Administrateur';
}

$dashboard_data = [];
if ($_SESSION['role'] == 'etudiant') {
    $stmt_candidatures = $pdo->prepare("
        SELECT c.statut, o.titre, o.date_debut, o.date_fin, e.nom as entreprise_nom
        FROM candidatures c 
        JOIN offres_stages o ON c.offre_id = o.id 
        JOIN entreprises e ON o.entreprise_id = e.id
        WHERE c.etudiant_id = ? 
        ORDER BY c.date_candidature DESC 
        LIMIT 3
    ");
    $stmt_candidatures->execute([$_SESSION['user_id']]);
    $dernieres_candidatures = $stmt_candidatures->fetchAll(PDO::FETCH_ASSOC);

    $stmt_offres = $pdo->prepare("
        SELECT id, titre, description, date_debut, date_fin 
        FROM offres_stages 
        ORDER BY date_debut DESC 
        LIMIT 3
    ");
    $stmt_offres->execute();
    $offres_recommandees = $stmt_offres->fetchAll(PDO::FETCH_ASSOC);

    $stmt_messages = $pdo->prepare("
        SELECT DISTINCT e.id, e.nom as expediteur_nom, COUNT(m.id) as nb_messages
        FROM messages m
        JOIN entreprises e ON CONCAT('C', e.id) = m.expediteur_id
        WHERE m.destinataire_id = CONCAT('E', :user_id)
        AND m.statut = 'non_lu'
        GROUP BY e.id, e.nom
        ORDER BY MAX(m.date_envoi) DESC
    ");
    $stmt_messages->execute(['user_id' => $_SESSION['user_id']]);
    $messages_non_lus = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

    $stmt_candidatures_updated = $pdo->prepare("
        SELECT c.id, c.statut, c.date_modification, o.titre, e.nom as entreprise_nom 
        FROM candidatures c
        JOIN offres_stages o ON c.offre_id = o.id
        JOIN entreprises e ON o.entreprise_id = e.id
        WHERE c.etudiant_id = :user_id 
        AND c.statut IN ('acceptee', 'refusee')
        AND (c.date_lecture IS NULL OR c.date_modification > c.date_lecture)
        ORDER BY c.date_modification DESC
    ");
    $stmt_candidatures_updated->execute(['user_id' => $_SESSION['user_id']]);
    $candidatures_updated = $stmt_candidatures_updated->fetchAll(PDO::FETCH_ASSOC);

    $update_stmt = $pdo->prepare("
        UPDATE candidatures 
        SET date_lecture = CURRENT_TIMESTAMP 
        WHERE etudiant_id = ? AND date_lecture IS NULL
    ");
    $update_stmt->execute([$_SESSION['user_id']]);

    $dashboard_data = [
        'total_candidatures' => $pdo->query("SELECT COUNT(*) FROM candidatures WHERE etudiant_id = {$_SESSION['user_id']}")->fetchColumn(),
        'candidatures_en_cours' => $pdo->query("SELECT COUNT(*) FROM candidatures WHERE etudiant_id = {$_SESSION['user_id']} AND statut = 'en_attente'")->fetchColumn(),
        'dernieres_candidatures' => $dernieres_candidatures,
        'offres_recommandees' => $offres_recommandees,
        'messages_non_lus' => $messages_non_lus,
        'total_messages_non_lus' => array_sum(array_column($messages_non_lus, 'nb_messages')),
        'candidatures_updated' => $candidatures_updated,
        'total_candidatures_updated' => count($candidatures_updated)
    ];
    
} elseif ($_SESSION['role'] == 'entreprise') {
    $stmt_candidatures = $pdo->prepare("
        SELECT c.id, c.statut, e.nom, e.prenom, o.titre 
        FROM candidatures c 
        JOIN offres_stages o ON c.offre_id = o.id 
        JOIN etudiants e ON c.etudiant_id = e.id
        WHERE o.entreprise_id = ? 
        ORDER BY c.date_candidature DESC 
        LIMIT 3
    ");
    $stmt_candidatures->execute([$_SESSION['user_id']]);
    $dernieres_candidatures = $stmt_candidatures->fetchAll(PDO::FETCH_ASSOC);

    $stmt_messages = $pdo->prepare("
        SELECT DISTINCT e.id, CONCAT(e.nom, ' ', e.prenom) as expediteur_nom, COUNT(m.id) as nb_messages
        FROM messages m
        JOIN etudiants e ON CONCAT('E', e.id) = m.expediteur_id
        WHERE m.destinataire_id = CONCAT('C', :user_id)
        AND m.statut = 'non_lu'
        GROUP BY e.id, e.nom, e.prenom
        ORDER BY MAX(m.date_envoi) DESC
    ");
    $stmt_messages->execute(['user_id' => $_SESSION['user_id']]);
    $messages_non_lus = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

    $stmt_new_applications = $pdo->prepare("
        SELECT COUNT(*) as count_new, o.titre
        FROM candidatures c
        JOIN offres_stages o ON c.offre_id = o.id
        WHERE o.entreprise_id = :user_id
        AND c.date_lecture IS NULL
        GROUP BY o.id
    ");
    $stmt_new_applications->execute(['user_id' => $_SESSION['user_id']]);
    $new_applications = $stmt_new_applications->fetchAll(PDO::FETCH_ASSOC);

    $stmt_pending_applications = $pdo->prepare("
        SELECT COUNT(*) as count_pending, o.titre, o.id as offre_id
        FROM candidatures c
        JOIN offres_stages o ON c.offre_id = o.id
        WHERE o.entreprise_id = :user_id
        AND c.statut = 'en_attente'
        GROUP BY o.id, o.titre
    ");
    $stmt_pending_applications->execute(['user_id' => $_SESSION['user_id']]);
    $pending_applications = $stmt_pending_applications->fetchAll(PDO::FETCH_ASSOC);

    $dashboard_data = [
        'total_offres' => $pdo->query("SELECT COUNT(*) FROM offres_stages WHERE entreprise_id = {$_SESSION['user_id']}")->fetchColumn(),
        'candidatures_recues' => $pdo->query("SELECT COUNT(*) FROM candidatures c JOIN offres_stages o ON c.offre_id = o.id WHERE o.entreprise_id = {$_SESSION['user_id']}")->fetchColumn(),
        'dernieres_candidatures' => $dernieres_candidatures,
        'messages_non_lus' => $messages_non_lus,
        'total_messages_non_lus' => array_sum(array_column($messages_non_lus, 'nb_messages')),
        'new_applications' => $new_applications,
        'total_new_applications' => array_sum(array_column($new_applications, 'count_new')),
        'pending_applications' => $pending_applications,
        'total_pending_applications' => array_sum(array_column($pending_applications, 'count_pending'))
    ];
} elseif ($_SESSION['role'] == 'admin') {
    $derniers_utilisateurs = $pdo->query("
        (SELECT 'Étudiant' as type, nom, prenom, email, date_naissance as date_inscription FROM etudiants ORDER BY id DESC LIMIT 3)
        UNION
        (SELECT 'Entreprise', nom, '', email, date_creation as date_inscription FROM entreprises ORDER BY id DESC LIMIT 3)
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dernieres_offres = $pdo->query("
        SELECT o.titre, o.date_debut, e.nom as entreprise 
        FROM offres_stages o 
        JOIN entreprises e ON o.entreprise_id = e.id 
        ORDER BY o.date_debut DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dashboard_data = [
        'total_etudiants' => $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn(),
        'total_entreprises' => $pdo->query("SELECT COUNT(*) FROM entreprises")->fetchColumn(),
        'offres_actives' => $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn(),
        'candidatures_totales' => $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn(),
        'derniers_utilisateurs' => $derniers_utilisateurs,
        'dernieres_offres' => $dernieres_offres
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Mon espace</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($welcome_message) ?></h1>
        <nav>
    <button class="menu-toggle" aria-label="Ouvrir le menu">☰</button>
    <ul class="menu">
        <li><a href="/Gestion_Stage/app/views/profile.php">👤 Mon profil</a></li>
        <?php if ($_SESSION['role'] == 'etudiant'): ?>
            <li><a href="/Gestion_Stage/app/views/panels/student_panel.php">🔍 Offres de stages</a></li>
            <!-- <li><a href="/Gestion_Stage/app/message/inbox.php">📩 Mes Messages</a></li> -->
        <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
            <li><a href="/Gestion_Stage/app/views/internships/post_internship.php">➕ Publier une offre</a></li>
            <li><a href="/Gestion_Stage/app/views/panels/company_panel.php">📋 Gérer candidatures</a></li>
            <!-- <li><a href="/Gestion_Stage/app/message/inbox.php">📩 Mes Messages</a></li> -->
        <?php elseif ($_SESSION['role'] == 'admin'): ?>
            <li><a href="/Gestion_Stage/app/views/panels/admin_panel.php">🛠️ Panel Admin</a></li>
        <?php endif; ?>
        <li><a href="/Gestion_Stage/app/views/auth/logout.php">🚪 Déconnexion</a></li>
    </ul>
</nav>


<script>
document.querySelector('.menu-toggle').addEventListener('click', function() {
    const menu = document.querySelector('.menu');
    menu.classList.toggle('open');
});

window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        const menu = document.querySelector('.menu');
        menu.classList.remove('open');
    }
});


</script>
        

<?php if (isset($_SESSION['success'])): ?>
    <div class="notification success">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="notification error">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>


    <div class="dashboard-grid">
        <?php if ($_SESSION['role'] == 'etudiant'): ?>
            <div class="dashboard-card">
                <h3>Mes Candidatures</h3>
                <p>Total : <?= $dashboard_data['total_candidatures'] ?></p>
                <p>En cours : <?= $dashboard_data['candidatures_en_cours'] ?></p>
            </div>
            <div class="dashboard-card">
            <h3>Dernières Candidatures</h3>
                <ul class="dashboard-list">
                    <?php if (empty($dashboard_data['dernieres_candidatures'])): ?>
                        <li>Aucune candidatures récentes</li>
                    <?php else: ?>
                        <?php foreach($dashboard_data['dernieres_candidatures'] as $candidature): ?>
                            <li>
                                <?= htmlspecialchars($candidature['entreprise_nom']) . ' - ' . htmlspecialchars($candidature['titre']) . ' - '?>
                                <span class="<?= $candidature['statut'] == 'en_attente' ? 'text-warning' : ($candidature['statut'] == 'refusee' ? 'text-refuse' : 'text-success') ?>">
                                    <?= formatStatus($candidature['statut']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="dashboard-card">
                <h3>Offres Recommandées</h3>
                <ul class="dashboard-list">
                    <?php foreach($dashboard_data['offres_recommandees'] as $offre): ?>
                        <li>
                            <?= htmlspecialchars($offre['titre']) ?> 
                            <small>(<?= date('d/m/Y', strtotime($offre['date_debut'])) ?> - <?= date('d/m/Y', strtotime($offre['date_fin'])) ?>)</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($_SESSION['role'] == 'entreprise'): ?>
            <div class="dashboard-card">
                <h3>Mes Offres</h3>
                <p>Total : <?= $dashboard_data['total_offres'] ?></p>
                <p>Candidatures : <?= $dashboard_data['candidatures_recues'] ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Dernières Candidatures</h3>
                <ul class="dashboard-list">
                    <?php if (empty($dashboard_data['dernieres_candidatures'])): ?>
                        <li>Aucune candidatures récentes</li>
                    <?php else: ?>
                        <?php foreach($dashboard_data['dernieres_candidatures'] as $candidature): ?>
                            <li>
                                <?= htmlspecialchars($candidature['nom'] . ' ' . $candidature['prenom']) ?> 
                                - <?= htmlspecialchars($candidature['titre']) . ' - '?>
                                <span class="<?= $candidature['statut'] == 'en_attente' ? 'text-warning' : ($candidature['statut'] == 'refusee' ? 'text-refuse' : 'text-success') ?>">
                                    <?= formatStatus($candidature['statut']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        <?php elseif ($_SESSION['role'] == 'admin'): ?>
            <div class="dashboard-card">
                <h3>Utilisateurs</h3>
                <p>Étudiants : <?= $dashboard_data['total_etudiants'] ?></p>
                <p>Entreprises : <?= $dashboard_data['total_entreprises'] ?></p>
                <p>Offres : <?= $dashboard_data['offres_actives'] ?></p>
                <p>Candidatures : <?= $dashboard_data['candidatures_totales'] ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Derniers Utilisateurs</h3>
                <ul class="dashboard-list">
                    <?php foreach($dashboard_data['derniers_utilisateurs'] as $utilisateur): ?>
                        <li>
                            <?= htmlspecialchars($utilisateur['type']) ?> : 
                            <?= htmlspecialchars($utilisateur['nom'] . ' ' . $utilisateur['prenom']) ?>
                            <small>(<?= date('d/m/Y', strtotime($utilisateur['date_inscription'])) ?>)</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="dashboard-card">
                <h3>Dernières Offres</h3>
                <ul class="dashboard-list">
                    <?php foreach($dashboard_data['dernieres_offres'] as $offre): ?>
                        <li>
                            <?= htmlspecialchars($offre['titre']) ?> 
                            <small>par <?= htmlspecialchars($offre['entreprise']) ?></small>
                            <br><small><?= date('d/m/Y', strtotime($offre['date_debut'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="dashboard-card">
            <h3>Date</h3>
            <p>
                <i class="far fa-clock"></i>
                <?php
                    setlocale(LC_TIME, 'fr_FR.UTF-8');
                    echo strftime('%A %d %B %Y');
                ?>
            </p>
        </div>
        <?php if (in_array($_SESSION['role'], ['etudiant', 'entreprise'])): ?>
    <div class="dashboard-card">
        <h3>Notifications</h3>
        <ul class="dashboard-list">
            <?php if ($_SESSION['role'] == 'etudiant'): ?>
                <?php if ($dashboard_data['total_messages_non_lus'] > 0 || $dashboard_data['total_candidatures_updated'] > 0): ?>
                    <?php if ($dashboard_data['total_messages_non_lus'] > 0): ?>
                        <li>
                            <i class="fas fa-envelope"></i>
                            Vous avez <?= $dashboard_data['total_messages_non_lus'] ?> nouveau(x) message(s)
                            <ul>
                                <?php foreach ($dashboard_data['messages_non_lus'] as $message): ?>
                                    <li>
                                        <a href="/Gestion_Stage/app/message/inbox.php?entreprise_id=<?= $message['id'] ?>">
                                            <?= htmlspecialchars($message['expediteur_nom']) ?>
                                            <span class="notification-badge"><?= $message['nb_messages'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($dashboard_data['total_candidatures_updated'] > 0): ?>
                        <li>
                            <i class="fas fa-bell"></i>
                            Mises à jour de vos candidatures:
                            <ul>
                            <?php foreach ($dashboard_data['candidatures_updated'] as $candidature): ?>
                                <li>
                                    <a href="/Gestion_Stage/app/views/panels/student_panel.php" 
                                    onclick="event.preventDefault(); markAsReadAndRedirect(<?= $candidature['id'] ?>, this.href);">
                                        <?= htmlspecialchars($candidature['entreprise_nom']) ?> - 
                                        <?= htmlspecialchars($candidature['titre']) ?> : 
                                        <span class="text-<?= 
                                            $candidature['statut'] === 'acceptee' ? 'success' : 
                                            ($candidature['statut'] === 'en_attente' ? 'warning' : 'refuse') 
                                        ?>">
                                            <?= formatStatus($candidature['statut']) ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>Aucune nouvelle notification</li>
                <?php endif; ?>

            <?php else: /* Entreprise */ ?>
                <?php if ($dashboard_data['total_messages_non_lus'] > 0 || $dashboard_data['total_pending_applications'] > 0): ?>
                    <?php if ($dashboard_data['total_messages_non_lus'] > 0): ?>
                        <li>
                            <i class="fas fa-envelope"></i>
                            Vous avez <?= $dashboard_data['total_messages_non_lus'] ?> nouveau(x) message(s)
                            <ul>
                                <?php foreach ($dashboard_data['messages_non_lus'] as $message): ?>
                                    <li>
                                        <a href="/Gestion_Stage/app/message/inbox.php?etudiant_id=<?= $message['id'] ?>">
                                            <?= htmlspecialchars($message['expediteur_nom']) ?>
                                            <span class="notification-badge"><?= $message['nb_messages'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($dashboard_data['total_pending_applications'] > 0): ?>
                        <li>
                            <i class="fas fa-clock"></i>
                            Candidatures en attente:
                            <ul>
                                <?php foreach ($dashboard_data['pending_applications'] as $application): ?>
                                    <li>
                                        <a href="/Gestion_Stage/app/views/internships/view_applications.php?offre_id=<?= $application['offre_id'] ?>" style="color: #000;">
                                            <?= htmlspecialchars($application['titre']) ?>
                                            <span class="notification-badge pending"><?= $application['count_pending'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>Aucune nouvelle notification</li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
    </div>

        <p><a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a></p>
    </div>

    <button class="feedback-button" onclick="document.getElementById('feedback-modal').style.display='block'">
        <i class="fas fa-comment-alt"></i>
    </button>

    <div id="feedback-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('feedback-modal').style.display='none'">&times;</span>
            <h2>Votre avis compte</h2>
            <p>Donnez votre avis sur notre site.</p>
            <form action="/Gestion_Stage/app/views/feedback/submit_feedback.php" method="post">
                <input type="hidden" name="user_name" value="<?= htmlspecialchars($welcome_message) ?>">
                <input type="hidden" name="role" value="<?= htmlspecialchars($_SESSION['role']) ?>">
                <textarea name="feedback" placeholder="Votre avis..." required></textarea>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <script>
        window.onclick = function(event) {
            if (event.target == document.getElementById('feedback-modal')) {
                document.getElementById('feedback-modal').style.display = 'none';
            }
        }

        function markAsRead(candidatureId) {
            fetch('/Gestion_Stage/app/helpers/mark_candidature_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'candidature_id=' + candidatureId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function markAsReadAndRedirect(candidatureId, redirectUrl) {
            fetch('/Gestion_Stage/app/helpers/mark_candidature_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'candidature_id=' + candidatureId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = redirectUrl;
                }
            });
        }
</script>
</body>
</html>