<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$entreprise_id = $_SESSION['user_id'];
$offre_id = $_GET['id'] ?? 0;

// Récupérer les détails de l'offre
$stmt = $pdo->prepare("SELECT * FROM offres_stages WHERE id = ? AND entreprise_id = ?");
$stmt->execute([$offre_id, $entreprise_id]);
$offre = $stmt->fetch();

if (!$offre) {
    header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $stmt = $pdo->prepare("UPDATE offres_stages SET titre = ?, description = ?, date_debut = ?, date_fin = ? WHERE id = ? AND entreprise_id = ?");
    if ($stmt->execute([$titre, $description, $date_debut, $date_fin, $offre_id, $entreprise_id])) {
        header("Location: /Gestion_Stage/app/views/panels/company_panel.php");
        exit();
    } else {
        $error = "Erreur lors de la mise à jour de l'offre de stage.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une offre de stage</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Modifier une offre de stage</h1>
        </div>
    </header>

    <main class="container">
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="post">
            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($offre['titre']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($offre['description']); ?></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" value="<?php echo $offre['date_debut']; ?>" required>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" value="<?php echo $offre['date_fin']; ?>" required>

            <button type="submit" class="btn-primary">Mettre à jour l'offre de stage</button>
        </form>
        <p><a href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a></p>
    </main>
</body>
</html>