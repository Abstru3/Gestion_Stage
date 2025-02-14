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

// Récupérer les statistiques et les données pour l'administration
$total_users = $pdo->query("SELECT (SELECT COUNT(*) FROM etudiants) + (SELECT COUNT(*) FROM entreprises)")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn();
$total_applications = $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$total_companies = $pdo->query("SELECT COUNT(*) FROM entreprises")->fetchColumn();

// Modifier la requête de récupération des utilisateurs
$users = $pdo->query("
    SELECT id, email, telephone, adresse, 'etudiant' as role FROM etudiants
    UNION ALL
    SELECT id, email, telephone, adresse, 'entreprise' as role FROM entreprises
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Section pour gérer les entreprises en attente de validation
$stmt = $pdo->prepare("SELECT * FROM entreprises WHERE valide = FALSE");
$stmt->execute();
$entreprises_en_attente = $stmt->fetchAll();
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
        <a href="#entreprises_attente">Entreprises en attente</a>
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
                    // Initialiser DataTables sur le tableau
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

            <!-- Entreprises en attente de validation -->
            <div class="card p-4 shadow mt-4" id="entreprises_attente">
                <h2 class="mb-3">Entreprises en attente de validation</h2>
                <?php if (!empty($entreprises_en_attente)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>SIRET</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entreprises_en_attente as $entreprise): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entreprise['nom']) ?></td>
                                    <td><?= htmlspecialchars($entreprise['siret']) ?></td>
                                    <td><?= htmlspecialchars($entreprise['email']) ?></td>
                                    <td>
                                        <a href="/Gestion_Stage/app/views/company_profile.php?id=<?= $entreprise['id'] ?>" 
                                           class="btn btn-info btn-sm me-2">
                                            <i class="fas fa-eye"></i> Voir le profil
                                        </a>
                                        <form method="POST" action="/Gestion_Stage/app/controllers/validate_company.php" class="d-inline">
                                            <input type="hidden" name="entreprise_id" value="<?= $entreprise['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-check"></i> Valider
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune entreprise en attente de validation</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <p class="text-center mt-4">
        <a href="/Gestion_Stage/app/views/home.php" class="btn btn-primary">Espace personnel</a>
    </p>

    <script>
        // Toggle Sidebar on small screens
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        // Chart.js for the User Chart
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

    <!-- <div class="container mt-4">
    <h1 class="text-center mb-4">Gestion des Rôles des Utilisateurs</h1>

    <div class="card p-4 shadow mt-4">
        <h2 class="mb-3">Modifier les Rôles</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" action="update_role.php">
                                    <div class="input-group">
                                        <select name="role_id" class="form-control">
                                            <?php if (!empty($roles)) : ?>
                                                <?php foreach ($roles as $role) : ?>
                                                    <option value="<?= $role['id'] ?>" <?= $role['id'] == $user['role_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($role['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option>Aucun rôle disponible</option>
                                            <?php endif; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary btn-sm mt-1">Modifier</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                </form>
                            </td>
                            <td>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="4" class="text-center">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
