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

if ($role === 'etudiant') {
    $user_prefix = 'E';
    $contact_prefix = 'C';
} else {
    $user_prefix = 'C';
    $contact_prefix = 'E';
}

if ($role === 'entreprise') {
    $etudiant_query = "SELECT DISTINCT e.*, 
    CONCAT(e.nom, ' ', e.prenom) as nom_complet,
    MAX(m.date_envoi) as dernier_message,
    c.statut as statut_candidature,
    os.titre as titre_offre,
    MIN(m.conversation_id) as conversation_id,
    COUNT(DISTINCT CASE 
        WHEN m.statut = 'non_lu' 
        AND m.expediteur_id = CONCAT('E', e.id) 
        AND m.destinataire_id = CONCAT('C', :user_id) 
        THEN m.id 
    END) as messages_non_lus
FROM etudiants e
INNER JOIN candidatures c ON e.id = c.etudiant_id
INNER JOIN offres_stages os ON c.offre_id = os.id
LEFT JOIN messages m ON (
    m.expediteur_id = CONCAT('E', e.id) 
    AND m.destinataire_id = CONCAT('C', :user_id)
)
WHERE os.entreprise_id = :user_id
GROUP BY e.id";

    $etudiant_stmt = $pdo->prepare($etudiant_query);
    $etudiant_stmt->execute([':user_id' => $user_id]);
    $etudiants = $etudiant_stmt->fetchAll();
} else {
    $entreprise_query = "SELECT DISTINCT e.*, 
    MAX(m.date_envoi) as dernier_message,
    c.statut as statut_candidature,
    os.titre as titre_offre,
    COUNT(DISTINCT CASE 
        WHEN m.statut = 'non_lu' 
        AND m.expediteur_id = CONCAT('C', e.id) 
        AND m.destinataire_id = CONCAT('E', :user_id) 
        THEN m.id 
    END) as messages_non_lus
FROM entreprises e
LEFT JOIN offres_stages os ON e.id = os.entreprise_id
LEFT JOIN candidatures c ON os.id = c.offre_id AND c.etudiant_id = :user_id
LEFT JOIN messages m ON (
    m.expediteur_id = CONCAT('C', e.id) 
    AND m.destinataire_id = CONCAT('E', :user_id)
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
GROUP BY e.id";
    
    $entreprise_stmt = $pdo->prepare($entreprise_query);
    $entreprise_stmt->execute([':user_id' => $user_id]);
    $entreprises = $entreprise_stmt->fetchAll();
}

$conversation_id = null;
if ($role === 'entreprise' && $selected_etudiant_id) {
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
        
        if (!empty($messages)) {
            $entreprise_id = ($messages[0]['expediteur_id'] != $user_id) ? 
                            $messages[0]['expediteur_id'] : 
                            $messages[0]['destinataire_id'];
        }
    } else {
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
    $first_message_check_query = "SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY date_envoi ASC LIMIT 1";
    $first_message_check_stmt = $pdo->prepare($first_message_check_query);
    $first_message_check_stmt->execute([':conversation_id' => $conversation_id]);
    $first_message = $first_message_check_stmt->fetch();
    $can_reply = $first_message['expediteur_id'] == $selected_etudiant_id;
} else {
    $can_reply = false;
}

if ($role === 'etudiant' && $selected_entreprise_id) {
    $update_query = "UPDATE messages 
                    SET statut = 'lu' 
                    WHERE expediteur_id = CONCAT('C', :entreprise_id)
                    AND destinataire_id = CONCAT('E', :user_id)
                    AND statut = 'non_lu'";
    
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([
        ':entreprise_id' => $selected_entreprise_id,
        ':user_id' => $user_id
    ]);
} elseif ($role === 'entreprise' && $selected_etudiant_id) {
    $update_query = "UPDATE messages 
                    SET statut = 'lu' 
                    WHERE expediteur_id = CONCAT('E', :etudiant_id)
                    AND destinataire_id = CONCAT('C', :user_id)
                    AND statut = 'non_lu'";
    
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([
        ':etudiant_id' => $selected_etudiant_id,
        ':user_id' => $user_id
    ]);
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
    if (window.location.search) {
        loadMessages(true);
    }

    function scrollToBottom() {
        const messageContainer = document.querySelector('.messages');
        if (messageContainer) {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }
    }

    function loadMessages(isInitialLoad = false) {
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
                    $('.reply-section').show();
                    scrollToBottom();
                    initializeMessageForm();
                    
                    if (isInitialLoad) {
                        updateActiveConversation();
                        if (entrepriseId) {
                            $(`.conversation-item a[href*="entreprise_id=${entrepriseId}"]`).find('.notification-badge').remove();
                        } else if (etudiantId) {
                            $(`.conversation-item a[href*="etudiant_id=${etudiantId}"]`).find('.notification-badge').remove();
                        }
                    }
                }
            });
        } else {
            $('.reply-section').hide();
        }
    }

    function initializeMessageForm() {
        const form = $('#messageForm');
        if (!form.length) return;

        form.find('textarea[name="contenu"]').on('keydown', function(e) {
            if (e.key === 'Enter') {
                if (!e.shiftKey) {
                    e.preventDefault();
                    const contenu = $(this).val().trim();
                    if (contenu) {
                        form.submit();
                    }
                }
            }
        });

        form.off('submit').on('submit', function(e) {
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
                        setTimeout(scrollToBottom, 100);
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

    $(document).on('click', '.conversation-item a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const urlParams = new URLSearchParams(new URL(href, window.location.href).search);
        const entrepriseId = urlParams.get('entreprise_id');
        
        window.history.pushState({}, '', href);
        $(this).find('.notification-badge').remove();
        
        loadMessages(true);
        updateActiveConversation();
        
        if ('<?php echo $role; ?>' === 'etudiant' && entrepriseId) {
            if ($('.company-info-frame').length === 0) {
                $('body').append('<div class="company-info-frame" style="display: none;"></div>');
            }

            $.ajax({
                url: 'get_company_info.php',
                method: 'GET',
                data: { entreprise_id: entrepriseId },
                success: function(response) {
                    const companyInfoFrame = $('.company-info-frame');
                    companyInfoFrame.html(response);
                    companyInfoFrame.attr('style', `
                        display: block !important;
                        position: fixed;
                        top: 100px;
                        right: 2rem;
                        width: 250px;
                        background: white;
                        z-index: 1000;
                        opacity: 1;
                        padding: 1.5rem;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        border-radius: 10px;
                    `);
                },
                error: function(error) {
                    console.error('Erreur:', error);
                }
            });
        } else {
            $('.company-info-frame').hide();
        }
    });

    function updateActiveConversation() {
        const currentUrl = new URL(window.location.href);
        const etudiantId = currentUrl.searchParams.get('etudiant_id');
        const entrepriseId = currentUrl.searchParams.get('entreprise_id');

        $('.conversation-item').removeClass('active');

        if (etudiantId) {
            $(`.conversation-item a[href*="etudiant_id=${etudiantId}"]`).parent().addClass('active');
        } else if (entrepriseId) {
            $(`.conversation-item a[href*="entreprise_id=${entrepriseId}"]`).parent().addClass('active');
        }
    }

    $(document).on('click', '.conversation-item a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        window.history.pushState({}, '', href);
        updateActiveConversation();
        loadMessages();
    });

    updateActiveConversation();
    
    const conversationList = document.querySelector('.conversation-list');
    let isScrolling = false;
    let startX;
    let scrollLeft;

    conversationList.addEventListener('touchstart', (e) => {
        isScrolling = true;
        startX = e.touches[0].pageX - conversationList.offsetLeft;
        scrollLeft = conversationList.scrollLeft;
    });

    conversationList.addEventListener('touchmove', (e) => {
        if (!isScrolling) return;
        e.preventDefault();
        const x = e.touches[0].pageX - conversationList.offsetLeft;
        const walk = (x - startX) * 2;
        conversationList.scrollLeft = scrollLeft - walk;
    });

    conversationList.addEventListener('touchend', () => {
        isScrolling = false;
    });
});
</script>

</head>
<body>
    <div class="container inbox-container">
        <div class="conversation-list">
    <?php if ($role === 'entreprise'): ?>
        <h3>Conversations avec les candidats</h3>
        <ul class="conversations">
            <?php foreach ($etudiants as $etudiant): ?>
                <li class="conversation-item <?php echo $selected_etudiant_id == $etudiant['id'] ? 'active' : ''; ?>">
                    <a href="?etudiant_id=<?php echo $etudiant['id']; ?>">
                        <div class="avatar" data-name="<?php echo htmlspecialchars($etudiant['nom_complet']); ?>">
                            <i class="fas fa-user-graduate"></i>
                            <?php if ($etudiant['messages_non_lus'] > 0): ?>
                                <span class="notification-badge">
                                    <?php echo $etudiant['messages_non_lus'] > 9 ? '9+' : $etudiant['messages_non_lus']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <span class="name">
                                <?php echo htmlspecialchars($etudiant['nom_complet']); ?>
                                <?php if ($etudiant['messages_non_lus'] > 0): ?>
                                    <span class="notification-badge">
                                        <?php echo $etudiant['messages_non_lus'] > 9 ? '9+' : $etudiant['messages_non_lus']; ?>
                                    </span>
                                <?php endif; ?>
                            </span>
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
                                <div class="avatar" data-name="<?php echo htmlspecialchars($entreprise['nom']); ?>">
                                    <?php if ($entreprise['icone']): ?>
                                        <img src="/Gestion_Stage/public/uploads/profil/<?php echo htmlspecialchars($entreprise['icone']); ?>" 
                                             alt="Icône <?php echo htmlspecialchars($entreprise['nom']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-building"></i>
                                    <?php endif; ?>
                                    <?php if ($entreprise['messages_non_lus'] > 0): ?>
                                        <span class="notification-badge">
                                            <?php echo $entreprise['messages_non_lus'] > 9 ? '9+' : $entreprise['messages_non_lus']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="info">
                                    <span class="name">
                                        <?php echo htmlspecialchars($entreprise['nom']); ?>
                                        <?php if ($entreprise['messages_non_lus'] > 0): ?>
                                            <span class="notification-badge">
                                                <?php echo $entreprise['messages_non_lus'] > 9 ? '9+' : $entreprise['messages_non_lus']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
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
    <?php if (($role === 'etudiant' && !$selected_entreprise_id) || ($role === 'entreprise' && !$selected_etudiant_id)): ?>
        <p class="empty-message"><i class="fas fa-comments"></i> Sélectionnez une conversation pour afficher les messages</p>
    <?php elseif (empty($messages)): ?>
        <p class="empty-message"><i class="fas fa-envelope-open"></i> Aucun message dans cette conversation</p>
    <?php else: ?>
        <div class="messages-container">
            <ul class="messages">
                <?php 
                $current_date = null;
                foreach ($messages as $message): 
                    $message_date = date('Y-m-d', strtotime($message['date_envoi']));
                    if ($current_date !== $message_date):
                        $current_date = $message_date;
                ?>
                    <li class="date-separator">
                        <span>
                            <?php 
                            $today = date('Y-m-d');
                            $yesterday = date('Y-m-d', strtotime('-1 day'));
                            
                            if ($message_date === $today) {
                                echo 'Aujourd\'hui';
                            } elseif ($message_date === $yesterday) {
                                echo 'Hier';
                            } else {
                                echo date('d/m/Y', strtotime($message_date));
                            }
                            ?>
                        </span>
                    </li>
                <?php endif; ?>
                <li class="message <?php echo $message['statut'] === 'non_lu' ? 'unread' : ''; ?> 
                             <?php echo strpos($message['expediteur_id'], $user_prefix) === 0 ? 'sent' : 'received'; ?>">
                    <div class="message-header">
                        <span class="sender">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($message['expediteur_nom']); ?>
                        </span>
                        <span class="time">
                            <?php echo date('H:i', strtotime($message['date_envoi'])); ?>
                        </span>
                    </div>
                    <div class="message-body">
                        <?php echo nl2br(htmlspecialchars($message['contenu'])); ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

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

<?php if ($role === 'etudiant' && $selected_entreprise_id): ?>
    <div class="company-info-frame">
        <?php if ($entreprise['icone']): ?>
            <img src="/Gestion_Stage/public/uploads/profil/<?php echo htmlspecialchars($entreprise['icone']); ?>" 
                 alt="Logo <?php echo htmlspecialchars($entreprise['nom']); ?>">
        <?php else: ?>
            <i class="fas fa-building"></i>
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($entreprise['nom']); ?></h3>
        <a href="/Gestion_Stage/app/views/company_profile.php?id=<?php echo $selected_entreprise_id; ?>" class="btn-profile">
            <i class="fas fa-building"></i> Accéder au profil
        </a>
    </div>
<?php endif; ?>

    </div>
    <p class="index-button"><a class="index-button" href="/Gestion_Stage/app/views/home.php"><i class="fas fa-arrow-left"></i> Retour à l'espace personnel</a></p>
</body>
</html>
