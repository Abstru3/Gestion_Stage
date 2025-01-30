<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Validation des champs
    if (empty($titre) || empty($description) || empty($date_debut) || empty($date_fin)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (strtotime($date_fin) <= strtotime($date_debut)) {
        $error = "La date de fin doit être postérieure à la date de début.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO offres_stages (entreprise_id, titre, description, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$_SESSION['user_id'], $titre, $description, $date_debut, $date_fin]);
            
            if ($result) {
                $success = "L'offre de stage a été publiée avec succès.";
                // Redirection après 2 secondes
                header("refresh:2;url=company_panel.php");
            } else {
                $error = "Une erreur est survenue lors de la publication de l'offre.";
            }
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une offre de stage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Publier une offre de stage</h1>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><a href="company_panel.php">Retour au panneau entreprise</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main class="container">
        <?php
        if ($error) {
            echo "<p class='error'>$error</p>";
        }
        if ($success) {
            echo "<p class='success'>$success</p>";
        }
        ?>
        <form action="" method="post">
            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" required>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" required>

            <button type="submit" class="btn-primary">Publier l'offre de stage</button>
        </form>
    </main>
</body>
</html>