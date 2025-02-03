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
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn();
$total_applications = $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'étudiant'")->fetchColumn();
$total_companies = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'entreprise'")->fetchColumn();

// Récupérer la liste des utilisateurs
$users = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center mb-4">Panneau d'administration</h1>
        
        <!-- Tableau interactif des utilisateurs -->
        <div class="card p-4 shadow mt-4">
            <h2 class="mb-3">Liste des Utilisateurs</h2>
            <table id="usersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Section Statistiques -->
        <div class="card p-4 mt-4 shadow">
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

        <!-- Section Graphique Répartition des Utilisateurs -->
        <div class="card p-4 mt-4 shadow">
            <h2 class="mb-3">Répartition des Utilisateurs</h2>
            <canvas id="userChart"></canvas>
        </div>

        <p class="text-center mt-4">
            <a href="/Gestion_Stage/app/views/home.php" class="btn btn-primary">Retour à l'accueil</a>
        </p>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/French.json"
                }
            });

            // Graphique des utilisateurs
            const ctx = document.getElementById('userChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Étudiants', 'Entreprises'],
                    datasets: [{
                        data: [<?php echo $total_students; ?>, <?php echo $total_companies; ?>],
                        backgroundColor: ['#36A2EB', '#FF6384']
                    }]
                }
            });
        });
    </script>
</body>
</html>
