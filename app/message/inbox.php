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
$selected_entreprise_id = isset($_GET['entreprise_id']) ? intval($_GET['entreprise_id']) : null;
$selected_etudiant_id = isset($_GET['etudiant_id']) ? intval($_GET['etudiant_id']) : null;
$messages = [];

// Au début du fichier, après les includes, ajoutez :
if ($role === 'entreprise') {
    // Récupérer uniquement les étudiants qui ont candidaté aux offres de l'entreprise
    $etudiant_query = "SELECT DISTINCT e.*, 
                              CONCAT(e.nom, ' ', e.prenom) as nom_complet,
                              MAX(m.date_envoi) as dernier_message,
                              c.statut as statut_candidature,
                              os.titre as titre_offre
                       FROM etudiants e
                       INNER JOIN candidatures c ON e.id = c.etudiant_id
                       INNER JOIN offres_stages os ON c.offre_id = os.id
                       LEFT JOIN messages m ON (
                           (m.expediteur_id = e.id AND m.destinataire_id = :user_id)
                           OR (m.expediteur_id = :user_id AND m.destinataire_id = e.id)
                       )
                       WHERE os.entreprise_id = :user_id
                       GROUP BY e.id
                       ORDER BY dernier_message DESC, c.date_candidature DESC";
    
    $etudiant_stmt = $pdo->prepare($etudiant_query);
    $etudiant_stmt->execute([':user_id' => $user_id]);
    $etudiants = $etudiant_stmt->fetchAll();
} else {
    // Récupérer toutes les entreprises qui ont des messages avec l'étudiant
    $entreprise_query = "SELECT DISTINCT e.*, 
                                MAX(m.date_envoi) as dernier_message 
                         FROM entreprises e
                         INNER JOIN messages m ON (m.expediteur_id = e.id OR m.destinataire_id = e.id)
                         WHERE m.expediteur_id = :user_id OR m.destinataire_id = :user_id
                         GROUP BY e.id
                         ORDER BY dernier_message DESC";
    $entreprise_stmt = $pdo->prepare($entreprise_query);
    $entreprise_stmt->execute([':user_id' => $user_id]);
    $entreprises = $entreprise_stmt->fetchAll();
}

// Vérifier si l'entreprise a initié la conversation
$conversation_id = null;
if ($role === 'entreprise' && $selected_etudiant_id) {
    // Récupérer la première conversation pour cet étudiant
    $conversation_check_query = "SELECT m.id 
                               FROM messages m
                               WHERE m.expediteur_id = :user_id 
                               AND m.destinataire_id = :etudiant_id
                               ORDER BY m.date_envoi ASC LIMIT 1";
    $conversation_check_stmt = $pdo->prepare($conversation_check_query);
    $conversation_check_stmt->execute([':user_id' => $user_id, ':etudiant_id' => $selected_etudiant_id]);
    $conversation_check = $conversation_check_stmt->fetch();
    $conversation_id = $conversation_check ? $conversation_check['id'] : null;
} else {
    // Récupérer les messages existants pour l'étudiant ou l'entreprise
    if ($role === 'etudiant') {
        $query = "SELECT m.*,
                        CASE 
                            WHEN m.expediteur_id = :user_id THEN 'Vous'
                            ELSE e.nom 
                        END as expediteur_nom,
                        e.id as entreprise_id
                 FROM messages m
                 LEFT JOIN entreprises e ON e.id = CASE 
                     WHEN m.expediteur_id = :user_id THEN m.destinataire_id
                     ELSE m.expediteur_id 
                 END
                 WHERE m.expediteur_id = :user_id 
                 OR m.destinataire_id = :user_id
                 ORDER BY m.date_envoi ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $messages = $stmt->fetchAll();
        
        // Récupérer l'ID de l'entreprise pour le formulaire de réponse
        if (!empty($messages)) {
            $entreprise_id = ($messages[0]['expediteur_id'] != $user_id) ? 
                            $messages[0]['expediteur_id'] : 
                            $messages[0]['destinataire_id'];
        }
    } else {
        // Pour les entreprises : garder le code existant
        $query = "SELECT m.*, 
                e.id as entreprise_id, 
                e.nom as expediteur_nom, 
                e.icone as entreprise_icone
         FROM messages m
         LEFT JOIN entreprises e ON m.expediteur_id = e.id
         LEFT JOIN etudiants es ON m.expediteur_id = es.id
         WHERE (m.destinataire_id = :user_id OR m.expediteur_id = :user_id) 
         ORDER BY m.date_envoi ASC";


        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $messages = $stmt->fetchAll();
    }
}

// Remplacer la section de récupération des messages par :
$messages = [];

if ($role === 'etudiant' && $selected_entreprise_id) {
    $query = "SELECT m.*,
                    CASE 
                        WHEN m.expediteur_id = :user_id THEN 'Vous'
                        ELSE e.nom 
                    END as expediteur_nom,
                    e.id as entreprise_id
             FROM messages m
             LEFT JOIN entreprises e ON e.id = CASE 
                 WHEN m.expediteur_id = :user_id THEN m.destinataire_id
                 ELSE m.expediteur_id 
             END
             WHERE (m.expediteur_id = :user_id AND m.destinataire_id = :entreprise_id)
             OR (m.expediteur_id = :entreprise_id AND m.destinataire_id = :user_id)
             ORDER BY m.date_envoi ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':entreprise_id' => $selected_entreprise_id
    ]);
    $messages = $stmt->fetchAll();
    $entreprise_id = $selected_entreprise_id;
} elseif ($role === 'entreprise' && $selected_etudiant_id) {
    $query = "SELECT m.*,
                    CASE 
                        WHEN m.expediteur_id = :user_id THEN 'Vous'
                        ELSE CONCAT(e.nom, ' ', e.prenom)
                    END as expediteur_nom
             FROM messages m
             LEFT JOIN etudiants e ON e.id = :selected_etudiant_id
             WHERE (m.expediteur_id = :user_id AND m.destinataire_id = :selected_etudiant_id)
             OR (m.expediteur_id = :selected_etudiant_id AND m.destinataire_id = :user_id)
             ORDER BY m.date_envoi ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':selected_etudiant_id' => $selected_etudiant_id
    ]);
    $messages = $stmt->fetchAll();
}

if ($role === 'etudiant' && $conversation_id) {
    // Vérifier si l'entreprise a initié la conversation
    $first_message_check_query = "SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY date_envoi ASC LIMIT 1";
    $first_message_check_stmt = $pdo->prepare($first_message_check_query);
    $first_message_check_stmt->execute([':conversation_id' => $conversation_id]);
    $first_message = $first_message_check_stmt->fetch();
    $can_reply = $first_message['expediteur_id'] == $selected_etudiant_id;
} else {
    $can_reply = false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Boîte de réception</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_message.css">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo_reduis.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <h1 align="center" >Boîte de réception</h1>
    <script>
    $(document).ready(function() {
        function scrollToBottom() {
            const messageContainer = document.querySelector('.message-content');
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        function loadMessages() {
            // Récupérer l'ID de l'URL ou du select
            const entrepriseId = new URLSearchParams(window.location.search).get('entreprise_id');
            const etudiantId = new URLSearchParams(window.location.search).get('etudiant_id');
            
            if ((etudiantId && '<?php echo $role; ?>' === 'entreprise') || 
                (entrepriseId && '<?php echo $role; ?>' === 'etudiant')) {
                $.ajax({
                    url: 'load_messages.php',
                    method: 'GET',
                    data: { 
                        entreprise_id: entrepriseId,
                        etudiant_id: etudiantId
                    },
                    success: function(response) {
                        $('#messageContent').html(response);
                        scrollToBottom();
                    }
                });
            }
        }

        // Gérer le clic sur un étudiant ou une entreprise
        $('.conversation-item a').click(function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            window.history.pushState({}, '', href);
            loadMessages();
        });

        $('#messageForm').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const currentUrl = window.location.href; // Sauvegarder l'URL actuelle
            
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        form[0].reset();
                        loadMessages(); // Recharger les messages
                    } else {
                        alert('Erreur lors de l\'envoi du message');
                    }
                },
                error: function() {
                    alert('Erreur lors de l\'envoi du message');
                }
            });
        });

        // Charger les messages initialement si une conversation est sélectionnée
        if (new URLSearchParams(window.location.search).get('entreprise_id') || 
            new URLSearchParams(window.location.search).get('etudiant_id')) {
            loadMessages();
            // Rafraîchir les messages toutes les 5 secondes
            setInterval(loadMessages, 5000);
        }
    });
</script>

</head>
<body>
    <div class="container inbox-container">
        <!-- Liste des conversations pour les entreprises -->
        <div class="conversation-list">
    <?php if ($role === 'entreprise'): ?>
        <h3>Conversations avec les candidats</h3>
        <ul class="conversations">
            <?php foreach ($etudiants as $etudiant): ?>
                <li class="conversation-item <?php echo $selected_etudiant_id == $etudiant['id'] ? 'active' : ''; ?>">
                    <a href="?etudiant_id=<?php echo $etudiant['id']; ?>">
                        <div class="avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="info">
                            <span class="name"><?php echo htmlspecialchars($etudiant['nom_complet']); ?></span>
                            <span class="offer-title"><?php echo htmlspecialchars($etudiant['titre_offre']); ?></span>
                            <span class="status <?php echo $etudiant['statut_candidature']; ?>">
                                <?php echo ucfirst($etudiant['statut_candidature']); ?>
                            </span>
                            <?php if ($etudiant['dernier_message']): ?>
                                <span class="last-message">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($etudiant['dernier_message'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h3>Conversations avec les entreprises</h3>
        <ul class="conversations">
            <?php foreach ($entreprises as $entreprise): ?>
                <li class="conversation-item <?php echo $selected_entreprise_id == $entreprise['id'] ? 'active' : ''; ?>">
                    <a href="?entreprise_id=<?php echo $entreprise['id']; ?>">
                        <div class="avatar">
                            <?php if ($entreprise['logo']): ?>
                                <img src="/Gestion_Stage/public/uploads/logos/<?php echo htmlspecialchars($entreprise['logo']); ?>" 
                                     alt="Logo <?php echo htmlspecialchars($entreprise['nom']); ?>">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <span class="name"><?php echo htmlspecialchars($entreprise['nom']); ?></span>
                            <span class="last-message">
                                <?php echo date('d/m/Y H:i', strtotime($entreprise['dernier_message'])); ?>
                            </span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
        
        <div class="message-content" id="messageContent" style="height: 400px; overflow-y: auto;">
    <?php if ($role === 'etudiant' && !$selected_entreprise_id): ?>
        <p class="empty-message"><i class="fas fa-comments"></i> Sélectionnez une conversation pour afficher les messages</p>
    <?php elseif ($role === 'entreprise' && !$selected_etudiant_id): ?>
        <p class="empty-message"><i class="fas fa-comments"></i> Sélectionnez un étudiant pour afficher les messages</p>
    <?php elseif (empty($messages)): ?>
        <p class="empty-message"><i class="fas fa-envelope-open"></i> Aucun message dans cette conversation</p>
    <?php else: ?>
        <ul class="messages">
            <?php foreach ($messages as $message): ?>
                <li class="message <?php echo $message['statut'] === 'non_lu' ? 'unread' : ''; ?> 
                                 <?php echo $message['expediteur_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <div class="message-header">
                        <span class="sender">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($message['expediteur_nom']); ?>
                        </span>
                        <span class="date">
                            <i class="far fa-clock"></i>
                            <?php echo date('d/m/Y H:i', strtotime($message['date_envoi'])); ?>
                        </span>
                    </div>
                    <div class="message-body">
                        <?php echo nl2br(htmlspecialchars($message['contenu'])); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Zone de réponse -->
<div class="reply-section">
    <?php if ($role === 'entreprise' && !empty($selected_etudiant_id)): ?>
        <form id="messageForm" class="reply-box">
            <input type="hidden" name="destinataire_id" value="<?php echo $selected_etudiant_id; ?>">
            <textarea name="contenu" placeholder="Écrire un message..." required></textarea>
            <button type="submit" class="button">
                <i class="fas fa-paper-plane"></i> Envoyer
            </button>
        </form>
    <?php elseif ($role === 'etudiant' && isset($entreprise_id)): ?>
        <form id="messageForm" class="reply-box">
            <input type="hidden" name="destinataire_id" value="<?php echo $entreprise_id; ?>">
            <textarea name="contenu" placeholder="Écrire un message..." required></textarea>
            <button type="submit" class="button">
                <i class="fas fa-paper-plane"></i> Envoyer
            </button>
        </form>
    <?php endif; ?>
</div>
    </div>
    <p><a class="index-button" href="/Gestion_Stage/app/views/home.php"><i class="fas fa-arrow-left"></i> Retour à l'espace personnel</a></p>
</body>
</html>
