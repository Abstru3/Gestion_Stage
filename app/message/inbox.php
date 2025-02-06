<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['etudiant', 'entreprise'])) {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Récupérer les messages reçus
if ($role === 'etudiant') {
    $query = "SELECT m.*, e.nom as expediteur_nom 
              FROM messages m
              JOIN entreprises e ON m.expediteur_id = e.id
              WHERE m.destinataire_id = :user_id
              ORDER BY m.date_envoi DESC";
} else {
    $query = "SELECT m.*, CONCAT(e.nom, ' ', e.prenom) as expediteur_nom 
              FROM messages m
              JOIN etudiants e ON m.expediteur_id = e.id
              WHERE m.destinataire_id = :user_id
              ORDER BY m.date_envoi DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$messages = $stmt->fetchAll();

// Marquer les messages comme lus
$update_query = "UPDATE messages SET statut = 'lu' WHERE destinataire_id = :user_id AND statut = 'non_lu'";
$update_stmt = $pdo->prepare($update_query);
$update_stmt->execute([':user_id' => $user_id]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boîte de réception</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Boîte de réception</h1>
        <?php if ($role === 'entreprise'): ?>
            <a href="/Gestion_Stage/app/message/send-message.php" class="button">Envoyer un nouveau message</a>
        <?php endif; ?>
        
        <?php if (empty($messages)): ?>
            <p>Aucun message dans votre boîte de réception.</p>
        <?php else: ?>
            <ul class="message-list">
                <?php foreach ($messages as $message): ?>
                    <li class="message <?php echo $message['statut'] === 'non_lu' ? 'unread' : ''; ?>">
                        <div class="message-header">
                            <span class="sender"><?php echo htmlspecialchars($message['expediteur_nom']); ?></span>
                            <span class="date"><?php echo date('d/m/Y H:i', strtotime($message['date_envoi'])); ?></span>
                        </div>
                        <div class="message-content">
                            <?php echo htmlspecialchars($message['contenu']); ?>
                        </div>
                        <?php if ($role === 'etudiant'): ?>
                            <a href="/Gestion_Stage/app/message/reply.php?message_id=<?php echo $message['id']; ?>" class="button">Répondre</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <a href="/Gestion_Stage/app/views/home.php">Retour à l'accueil</a>
    </div>
</body>
</html>