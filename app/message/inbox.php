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

// Modifier la façon dont nous gérons les IDs
if ($role === 'etudiant') {
    $user_prefix = 'E';  // E pour Étudiant
    $contact_prefix = 'C'; // C pour Company/Entreprise
} else {
    $user_prefix = 'C';  // C pour Company/Entreprise
    $contact_prefix = 'E'; // E pour Étudiant
}

// Au début du fichier, après les includes, ajoutez :
if ($role === 'entreprise') {
    // Récupérer uniquement les étudiants qui ont candidaté aux offres de l'entreprise
    $etudiant_query = "SELECT DISTINCT e.*, 
                              CONCAT(e.nom, ' ', e.prenom) as nom_complet,
                              MAX(m.date_envoi) as dernier_message,
                              c.statut as statut_candidature,
                              os.titre as titre_offre,
                              MIN(m.conversation_id) as conversation_id
                       FROM etudiants e
                       INNER JOIN candidatures c ON e.id = c.etudiant_id
                       INNER JOIN offres_stages os ON c.offre_id = os.id
                       LEFT JOIN messages m ON (
                           (m.expediteur_id = CONCAT('E', e.id) AND m.destinataire_id = CONCAT('C', :user_id))
                           OR (m.expediteur_id = CONCAT('C', :user_id) AND m.destinataire_id = CONCAT('E', e.id))
                       )
                       WHERE os.entreprise_id = :user_id
                       GROUP BY e.id
                       ORDER BY CASE 
                           WHEN MAX(m.date_envoi) IS NULL THEN 1 
                           ELSE 0 
                       END, 
                       MAX(m.date_envoi) DESC, 
                       c.date_candidature DESC";

    $etudiant_stmt = $pdo->prepare($etudiant_query);
    $etudiant_stmt->execute([':user_id' => $user_id]);
    $etudiants = $etudiant_stmt->fetchAll();
} else {
    // Remplacer la requête pour les étudiants
    $entreprise_query = "SELECT DISTINCT e.*, 
                            MAX(m.date_envoi) as dernier_message,
                            c.statut as statut_candidature,
                            os.titre as titre_offre
                     FROM entreprises e
                     LEFT JOIN offres_stages os ON e.id = os.entreprise_id
                     LEFT JOIN candidatures c ON os.id = c.offre_id AND c.etudiant_id = :user_id
                     LEFT JOIN messages m ON (
                         (m.expediteur_id = CONCAT('E', :user_id) AND m.destinataire_id = CONCAT('C', e.id))
                         OR (m.expediteur_id = CONCAT('C', e.id) AND m.destinataire_id = CONCAT('E', :user_id))
                     )
                     WHERE 
                         (c.statut = 'acceptee' OR m.id IS NOT NULL)
                         AND e.id IN (
                             SELECT DISTINCT entreprise_id 
                             FROM offres_stages 
                             WHERE id IN (
                                 SELECT offre_id 
                                 FROM candidatures 
                                 WHERE etudiant_id = :user_id
                             )
                         )
                     GROUP BY e.id
                     ORDER BY CASE 
                         WHEN MAX(m.date_envoi) IS NULL THEN 1 
                         ELSE 0 
                     END,
                     MAX(m.date_envoi) DESC, 
                     c.date_candidature DESC";
    
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
                        WHEN m.expediteur_id = CONCAT('E', :user_id) THEN 'Vous'
                        ELSE e.nom 
                    END as expediteur_nom,
                    e.id as entreprise_id
             FROM messages m
             LEFT JOIN entreprises e ON SUBSTRING(m.expediteur_id, 2) = e.id 
             WHERE (m.expediteur_id = CONCAT('E', :user_id) AND m.destinataire_id = CONCAT('C', :entreprise_id))
             OR (m.expediteur_id = CONCAT('C', :entreprise_id) AND m.destinataire_id = CONCAT('E', :user_id))
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
                        WHEN m.expediteur_id = CONCAT('C', :user_id) THEN 'Vous'
                        ELSE CONCAT(e.nom, ' ', e.prenom)
                    END as expediteur_nom
             FROM messages m
             LEFT JOIN etudiants e ON SUBSTRING(m.expediteur_id, 2) = e.id
             WHERE (m.expediteur_id = CONCAT('C', :user_id) AND m.destinataire_id = CONCAT('E', :selected_etudiant_id))
             OR (m.expediteur_id = CONCAT('E', :selected_etudiant_id) AND m.destinataire_id = CONCAT('C', :user_id))
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
    <h1>Boîte de réception</h1>
    <script>
$(document).ready(function() {
    function scrollToBottom() {
        const messageContainer = document.querySelector('.messages');
        if (messageContainer) {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }
    }

    function loadMessages() {
        const entrepriseId = new URLSearchParams(window.location.search).get('entreprise_id');
        const etudiantId = new URLSearchParams(window.location.search).get('etudiant_id');
        
        if ((etudiantId && '<?php echo $role; ?>' === 'entreprise') || 
            (entrepriseId && '<?php echo $role; ?>' === 'etudiant')) {
            $('.reply-section').show();
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
                    initializeMessageForm();
                }
            });
        } else {
            $('.reply-section').hide();
        }
    }

    function initializeMessageForm() {
        $('#messageForm').off('submit');
        
        $('#messageForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const contenu = form.find('textarea[name="contenu"]').val();
            let destinataireId;
            
            if ('<?php echo $role; ?>' === 'etudiant') {
                destinataireId = new URLSearchParams(window.location.search).get('entreprise_id');
            } else {
                destinataireId = new URLSearchParams(window.location.search).get('etudiant_id');
            }
            
            if (!destinataireId || !contenu) {
                alert('Veuillez écrire un message');
                return;
            }
            
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    destinataire_id: destinataireId,
                    contenu: contenu
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        form[0].reset();
                        loadMessages();
                        setTimeout(scrollToBottom, 100); // Ajouter un petit délai
                    } else {
                        alert('Erreur lors de l\'envoi du message: ' + (response.message || ''));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de l\'envoi du message');
                }
            });
        });
    }

    // Gérer le clic sur une conversation
    $(document).on('click', '.conversation-item a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        window.history.pushState({}, '', href);
        loadMessages();
    });

    // Initialisation au chargement de la page
    if (new URLSearchParams(window.location.search).get('entreprise_id') || 
        new URLSearchParams(window.location.search).get('etudiant_id')) {
        loadMessages();
        initializeMessageForm();
        setInterval(loadMessages, 50000);
    }

    // Fonction pour mettre à jour la classe active
    function updateActiveConversation() {
        const currentUrl = new URL(window.location.href);
        const etudiantId = currentUrl.searchParams.get('etudiant_id');
        const entrepriseId = currentUrl.searchParams.get('entreprise_id');

        // Supprimer la classe active de toutes les conversations
        $('.conversation-item').removeClass('active');

        // Ajouter la classe active à la conversation sélectionnée
        if (etudiantId) {
            $(`.conversation-item a[href*="etudiant_id=${etudiantId}"]`).parent().addClass('active');
        } else if (entrepriseId) {
            $(`.conversation-item a[href*="entreprise_id=${entrepriseId}"]`).parent().addClass('active');
        }
    }

    // Mettre à jour lors du clic sur une conversation
    $(document).on('click', '.conversation-item a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        window.history.pushState({}, '', href);
        updateActiveConversation();
        loadMessages();
    });

    // Mettre à jour au chargement de la page
    updateActiveConversation();
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
                            <span class="status <?php echo strtolower($etudiant['statut_candidature']); ?>">
                                <?php 
                                $statut = strtolower($etudiant['statut_candidature']);
                                switch($statut) {
                                    case 'acceptee':
                                        echo '<i class="fas fa-check-circle"></i> Acceptée';
                                        break;
                                    case 'en_attente':
                                        echo '<i class="fas fa-clock"></i> En attente';
                                        break;
                                    case 'refusee':
                                        echo '<i class="fas fa-times-circle"></i> Refusée';
                                        break;
                                    default:
                                        echo ucfirst($statut);
                                }
                                ?>
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
        <!-- Remplacer la section d'affichage des entreprises par : -->
        <?php if ($role === 'etudiant'): ?>
            <h3>Mes conversations</h3>
            <ul class="conversations">
                <?php if (empty($entreprises)): ?>
                    <li class="empty-conversations">
                        <i class="fas fa-info-circle"></i>
                        Aucune conversation disponible
                    </li>
                <?php else: ?>
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
                                    <?php if ($entreprise['titre_offre']): ?>
                                        <span class="offer-title"><?php echo htmlspecialchars($entreprise['titre_offre']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($entreprise['statut_candidature']): ?>
                                        <span class="status <?php echo strtolower($entreprise['statut_candidature']); ?>">
                                            <?php 
                                            $statut = strtolower($entreprise['statut_candidature']);
                                            switch($statut) {
                                                case 'acceptee':
                                                    echo '<i class="fas fa-check-circle"></i> Acceptée';
                                                    break;
                                                case 'en_attente':
                                                    echo '<i class="fas fa-clock"></i> En attente';
                                                    break;
                                                case 'refusee':
                                                    echo '<i class="fas fa-times-circle"></i> Refusée';
                                                    break;
                                                default:
                                                    echo ucfirst($statut);
                                            }
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($entreprise['dernier_message']): ?>
                                        <span class="last-message">
                                            <i class="far fa-clock"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($entreprise['dernier_message'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>
        
        <div class="message-content" id="messageContent">
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

<!-- Modifier la section reply-section -->
<div class="reply-section" style="display: none;">
    <form id="messageForm" class="reply-box">
        <?php if ($role === 'etudiant'): ?>
            <input type="hidden" name="destinataire_id" value="<?php echo htmlspecialchars($selected_entreprise_id); ?>">
        <?php else: ?>
            <input type="hidden" name="destinataire_id" value="<?php echo htmlspecialchars($selected_etudiant_id); ?>">
        <?php endif; ?>
        <textarea name="contenu" placeholder="Écrire un message..." required></textarea>
        <button type="submit" class="button send-btn">
            <i class="fas fa-paper-plane"></i> Envoyer
        </button>
    </form>
</div>

    </div>
    <p class="index-button"><a class="index-button" href="/Gestion_Stage/app/views/home.php"><i class="fas fa-arrow-left"></i> Retour à l'espace personnel</a></p>
</body>
</html>
