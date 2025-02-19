<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$total_users = $pdo->query("SELECT (SELECT COUNT(*) FROM etudiants) + (SELECT COUNT(*) FROM entreprises)")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn();
$total_applications = $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$total_companies = $pdo->query("SELECT COUNT(*) FROM entreprises")->fetchColumn();

$users = $pdo->query("
    SELECT id, email, telephone, adresse, 'etudiant' as role FROM etudiants
    UNION ALL
    SELECT id, email, telephone, adresse, 'entreprise' as role FROM entreprises
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM entreprises ORDER BY nom ASC");
$stmt->execute();
$entreprises = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Panneau d'administration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_admin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="#" class="navbar-brand text-center text-light">Admin Panel</a>
        <a href="#statistiques">Statistiques</a>
        <a href="#utilisateurs">Utilisateurs</a>
        <a href="#entreprises">Liste des entreprises</a>
        <a href="/Gestion_Stage/app/views/home.php">Espace personnel</a>
        <a href="/Gestion_Stage/index.php">Menu principal</a>
        <a href="/Gestion_Stage/app/views/auth/logout.php">Se déconnecter</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" onclick="toggleSidebar()">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Admin Panel</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container mt-4">
            <h1 class="text-center mb-4">Panneau d'administration</h1>
            
            <!-- Statistiques -->
            <div class="card p-4 shadow mt-4" id="statistiques">
                <h2 class="mb-3">Statistiques</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nombre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nombre total d'utilisateurs</td>
                            <td><?php echo $total_users; ?></td>
                        </tr>
                        <tr>
                            <td>Nombre total d'offres de stages</td>
                            <td><?php echo $total_internships; ?></td>
                        </tr>
                        <tr>
                            <td>Nombre total de candidatures</td>
                            <td><?php echo $total_applications; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Graphique -->
            <div class="card p-4 mt-4 shadow">
                <h2 class="mb-3">Répartition des Utilisateurs</h2>
                <canvas id="userChart"></canvas>
            </div>

            <!-- Tableau interactif des utilisateurs -->
            <div class="card p-4 shadow mt-4" id="utilisateurs">
                <h2 class="mb-3">Liste des Utilisateurs</h2>
                <div class="table-container">
                    <table id="usersTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></td>
                                    <td><?= htmlspecialchars($user['adresse'] ?? 'Non renseigné') ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#usersTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "lengthMenu": [5, 10, 20],
                        "language": {
                            "search": "Rechercher :",
                            "paginate": {
                                "previous": "Précédent",
                                "next": "Suivant"
                            }
                        }
                    });
                });
            </script>

            <!-- Liste des entreprises -->
            <div class="card p-4 shadow mt-4" id="entreprises">
                <h2 class="mb-3">Liste des entreprises</h2>
                <?php if (!empty($entreprises)): ?>
                    <div class="table-container">
                        <table id="entreprisesTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>SIRET</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($entreprises as $entreprise): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entreprise['nom']) ?></td>
                                        <td><?= htmlspecialchars($entreprise['siret']) ?></td>
                                        <td><?= htmlspecialchars($entreprise['email']) ?></td>
                                        <td>
                                            <?php if ($entreprise['certification']): ?>
                                                <span class="badge bg-secondary"><i class="fas fa-certificate"></i> Certifiée</span>
                                            <?php elseif ($entreprise['valide']): ?>
                                                <span class="badge bg-success"><i class="fas fa-check"></i> Validée</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning"><i class="fas fa-clock"></i> En attente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/Gestion_Stage/app/views/company_profile.php?id=<?= $entreprise['id'] ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                
                                                <?php if (!$entreprise['valide']): ?>
                                                    <form method="POST" action="/Gestion_Stage/app/actions/validate_company.php" class="d-inline">
                                                        <input type="hidden" name="entreprise_id" value="<?= $entreprise['id'] ?>">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Valider
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($entreprise['valide'] && !$entreprise['certification']): ?>
                                                    <form method="POST" action="/Gestion_Stage/app/actions/certify_company.php" class="d-inline">
                                                        <input type="hidden" name="entreprise_id" value="<?= $entreprise['id'] ?>">
                                                        <button type="submit" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-certificate"></i> Certifier
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Aucune entreprise créée</p>
                <?php endif; ?>
            </div>

            <!-- Ajouter le script d'initialisation de DataTables pour le tableau des entreprises -->
            <script>
                $(document).ready(function() {
                    $('#entreprisesTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "lengthMenu": [5, 10, 20],
                        "language": {
                            "search": "Rechercher :",
                            "paginate": {
                                "previous": "Précédent",
                                "next": "Suivant"
                            },
                            "lengthMenu": "Afficher _MENU_ entrées",
                            "info": "Affichage de _START_ à _END_ sur _TOTAL_ entreprises",
                            "infoEmpty": "Aucune entreprise disponible",
                            "zeroRecords": "Aucune entreprise correspondante trouvée"
                        }
                    });
                });
            </script>
        </div>
    </div>

    <p class="text-center mt-4">
        <a href="/Gestion_Stage/app/views/home.php" class="btn btn-primary">Espace personnel</a>
    </p>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        const ctx = document.getElementById('userChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Étudiants', 'Entreprises'],
                datasets: [{
                    data: [<?php echo $total_students; ?>, <?php echo $total_companies; ?>],
                    backgroundColor: ['#F64C4C', '#366AED']
                }]
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
