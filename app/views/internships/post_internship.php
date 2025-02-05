<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Vérifier si l'utilisateur est connecté et est une entreprise
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $lieu = trim($_POST['lieu']);
    $mode_stage = $_POST['mode_stage'];
    $logo = $_FILES['logo'];

    if (empty($titre) || empty($description) || empty($date_debut) || empty($date_fin) || ($mode_stage == 'présentiel' && empty($lieu))) {
        $error = "Tous les champs sont obligatoires, notamment le lieu si le stage est en présentiel.";
    } elseif (strtotime($date_fin) <= strtotime($date_debut)) {
        $error = "La date de fin doit être postérieure à la date de début.";
    } else {
        // Vérifier si l'entreprise associée à l'utilisateur existe
        /*$stmt = $pdo->prepare("SELECT id FROM entreprises WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $entreprise_id = $stmt->fetchColumn();

        if (!$entreprise_id) {
            $error = "L'entreprise associée à ce compte n'existe pas.";
        } else {*/
            // Gestion du logo : téléchargement et vérification
            $logo_path = null;
            if ($logo['error'] == UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                if (in_array($logo['type'], $allowed_types)) {
                    // Vérifier si le répertoire existe, sinon le créer
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/uploads/logos/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);  // Créer le répertoire si nécessaire
                    }

                    $logo_path = 'uploads/logos/' . basename($logo['name']);
                    if (move_uploaded_file($logo['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $logo_path)) {
                        $success = "Logo téléchargé avec succès.";
                    } else {
                        $error = "Échec du téléchargement du logo.";
                    }
                } else {
                    $error = "Le fichier doit être une image de type JPG ou PNG.";
                }
            }

            try {
                // Insérer l'offre de stage avec le bon entreprise_id, lieu, mode_stage, et logo
                $stmt = $pdo->prepare("INSERT INTO offres_stages (entreprise_id, titre, description, date_debut, date_fin, lieu, mode_stage, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$_SESSION['user_id'], $titre, $description, $date_debut, $date_fin, $lieu, $mode_stage, $logo_path]);

                if ($result) {
                    $success = "L'offre de stage a été publiée avec succès.";
                    header("refresh:2;url=/Gestion_Stage/app/views/panels/company_panel.php");
                } else {
                    $error = "Une erreur est survenue lors de la publication de l'offre.";
                }
            } catch (PDOException $e) {
                $error = "Erreur de base de données : " . $e->getMessage();
            }
        //}
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une offre de stage</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Publier une offre de stage</h1>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><a class="index-button" href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a></li>
            <li><a href="/Gestion_Stage/app/views/panels/company_panel.php">Gérer les candidatures</a></li>
            
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">Déconnexion</a></li>
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
        <form action="" method="post" enctype="multipart/form-data">
            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" required>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" required>

            <label for="lieu">Lieu du stage:</label>
            <input type="text" id="lieu" name="lieu" placeholder="Lieu (si présentiel)" required>

            <label for="mode_stage">Mode de stage:</label>
            <select id="mode_stage" name="mode_stage" required>
                <option value="distanciel">Distanciel</option>
                <option value="présentiel">Présentiel</option>
            </select>

            <label for="logo">Logo de l'entreprise:</label>
            <input type="file" id="logo" name="logo" accept="image/*">

            <button type="submit" class="btn-primary">Publier l'offre de stage</button>
        </form>

        <script>
    // Script pour rendre le champ "lieu" obligatoire si "présentiel"
    document.getElementById('mode_stage').addEventListener('change', function () {
        const lieuField = document.getElementById('lieu');
        if (this.value === 'présentiel') {
            lieuField.required = true;
        } else {
            lieuField.required = false; 
        }
    });

    if (document.getElementById('mode_stage').value === 'présentiel') {
        document.getElementById('lieu').required = true;
    } else {
        document.getElementById('lieu').required = false;
    }
</script>


    </main>
</body>
</html>

