<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Vérifier que l'utilisateur est une entreprise
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

// Récupérer les étudiants qui ont postulé aux offres de l'entreprise
$stmt = $pdo->prepare("
    SELECT DISTINCT e.id, e.nom, e.prenom
    FROM etudiants e
    JOIN candidatures c ON e.id = c.etudiant_id
    JOIN offres_stages o ON c.offre_id = o.id
    WHERE o.entreprise_id = :entreprise_id
");
$stmt->execute(['entreprise_id' => $_SESSION['user_id']]);
$etudiants = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinataire_id = $_POST['destinataire_id'];
    $contenu = $_POST['contenu'];
    $expediteur_id = $_SESSION['user_id'];

    $query = "INSERT INTO messages (contenu, date_envoi, expediteur_id, destinataire_id, statut)
              VALUES (:contenu, NOW(), :expediteur_id, :destinataire_id, 'non_lu')";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([
        ':contenu' => $contenu,
        ':expediteur_id' => $expediteur_id,
        ':destinataire_id' => $destinataire_id
    ])) {
        // Créer une notification pour l'étudiant
        $notification_query = "INSERT INTO notifications (etudiant_id, message, date_notification, statut)
                               VALUES (:etudiant_id, :message, NOW(), 'non_lu')";
        $notification_stmt = $pdo->prepare($notification_query);
        $notification_stmt->execute([
            ':etudiant_id' => $destinataire_id,
            ':message' => "Vous avez reçu un nouveau message d'une entreprise."
        ]);

        header("Location: /Gestion_Stage/app/message/inbox.php");
        exit();
    } else {
        $error = "Une erreur est survenue lors de l'envoi du message.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Envoyer un message</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <h1>Envoyer un Message</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="post">
            <label for="destinataire_id">Sélectionnez un étudiant :</label>
            <select name="destinataire_id" id="destinataire_id" required>
                <option value="">-- Choisir un étudiant --</option>
                <?php foreach ($etudiants as $etudiant): ?>
                    <option value="<?php echo $etudiant['id']; ?>">
                        <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="contenu">Message :</label>
            <textarea name="contenu" id="contenu" rows="5" required></textarea>

            <button type="submit">Envoyer le message</button>
        </form>
        <a class="index-button" href="/Gestion_Stage/app/message/inbox.php">Retour à la boîte de réception</a>
    </div>
</body>
</html>