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

$message_id = $_GET['message_id'] ?? null;

if (!$message_id) {
    header("Location: /Gestion_Stage/app/message/inbox.php");
    exit();
}

// Récupérer le message original
$query = "SELECT m.*, e.id as entreprise_id, e.nom as entreprise_nom 
          FROM messages m
          JOIN entreprises e ON m.expediteur_id = e.id
          WHERE m.id = :message_id AND m.destinataire_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':message_id' => $message_id, ':user_id' => $_SESSION['user_id']]);
$original_message = $stmt->fetch();

if (!$original_message) {
    header("Location: /Gestion_Stage/app/message/inbox.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'];
    $expediteur_id = $_SESSION['user_id'];
    $destinataire_id = $original_message['entreprise_id'];

    $query = "INSERT INTO messages (contenu, date_envoi, expediteur_id, destinataire_id, statut)
              VALUES (:contenu, NOW(), :expediteur_id, :destinataire_id, 'non_lu')";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([
        ':contenu' => $contenu,
        ':expediteur_id' => $expediteur_id,
        ':destinataire_id' => $destinataire_id
    ])) {
        header("Location: /Gestion_Stage/app/message/inbox.php");
        exit();
    } else {
        $error = "Une erreur est survenue lors de l'envoi de la réponse.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Réponse</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <h1>Répondre au message</h1>
        <div class="original-message">
            <h2>Message original de <?php echo htmlspecialchars($original_message['entreprise_nom']); ?></h2>
            <p><?php echo htmlspecialchars($original_message['contenu']); ?></p>
        </div>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="post">
            <label for="contenu">Votre réponse :</label>
            <textarea name="contenu" id="contenu" rows="5" required></textarea>
            <button type="submit">Envoyer la réponse</button>
        </form>
        <a class="index-button" href="/Gestion_Stage/app/message/inbox.php">Retour à la boîte de réception</a>
    </div>
</body>
</html>