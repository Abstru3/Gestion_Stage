<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['etudiant', 'entreprise'])) {
    http_response_code(403);
    echo "Access denied";
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$selected_etudiant_id = isset($_GET['etudiant_id']) ? intval($_GET['etudiant_id']) : null;
$selected_entreprise_id = isset($_GET['entreprise_id']) ? intval($_GET['entreprise_id']) : null;

// Define prefixes based on role
$exp_prefix = $role === 'etudiant' ? 'E' : 'C';
$dest_prefix = $role === 'etudiant' ? 'C' : 'E';
$prefixed_user_id = $exp_prefix . $user_id;

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection not established.");
    }

    // Query to get messages based on role
    if ($role === 'etudiant') {
        if (!$selected_entreprise_id) {
            echo "<p class='empty-message'><i class='fas fa-comments'></i> Select a conversation to show messages</p>";
            exit;
        }
        
        $query = "SELECT m.*,
                    CASE 
                        WHEN m.expediteur_id = :prefixed_user_id THEN 'You'
                        ELSE e.nom 
                    END as expediteur_nom
                FROM messages m
                LEFT JOIN entreprises e ON CONCAT('C', e.id) = m.expediteur_id
                WHERE (m.expediteur_id = :prefixed_user_id AND m.destinataire_id = CONCAT('C', :entreprise_id))
                OR (m.expediteur_id = CONCAT('C', :entreprise_id) AND m.destinataire_id = :prefixed_user_id)
                ORDER BY m.date_envoi ASC";

        $params = [
            ':prefixed_user_id' => $prefixed_user_id,
            ':entreprise_id' => $selected_entreprise_id
        ];

    } else { // entreprise
        if (!$selected_etudiant_id) {
            echo "<p class='empty-message'><i class='fas fa-comments'></i> Select a student to show messages</p>";
            exit;
        }

        $query = "SELECT m.*,
                    CASE 
                        WHEN m.expediteur_id = :prefixed_user_id THEN 'You'
                        ELSE CONCAT(e.nom, ' ', e.prenom)
                    END as expediteur_nom
                FROM messages m
                LEFT JOIN etudiants e ON CONCAT('E', e.id) = m.expediteur_id
                WHERE (m.expediteur_id = :prefixed_user_id AND m.destinataire_id = CONCAT('E', :etudiant_id))
                OR (m.expediteur_id = CONCAT('E', :etudiant_id) AND m.destinataire_id = :prefixed_user_id)
                ORDER BY m.date_envoi ASC";

        $params = [
            ':prefixed_user_id' => $prefixed_user_id,
            ':etudiant_id' => $selected_etudiant_id
        ];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();

    if (empty($messages)) {
        echo "<div class='messages-container'>
                <ul class='messages'>
                    <li class='empty-message'><i class='fas fa-envelope-open'></i> Aucun message dans cette conversation</li>
                </ul>
              </div>";
    } else {
        echo "<div class='messages-container'><ul class='messages'>";
        
        $current_date = null;
        foreach ($messages as $message) {
            $message_date = date('Y-m-d', strtotime($message['date_envoi']));
            
            if ($current_date !== $message_date) {
                $current_date = $message_date;
                $display_date = '';
                
                if ($message_date === date('Y-m-d')) {
                    $display_date = 'Aujourd\'hui';
                } elseif ($message_date === date('Y-m-d', strtotime('-1 day'))) {
                    $display_date = 'Hier';
                } else {
                    $display_date = date('d/m/Y', strtotime($message_date));
                }
                
                echo "<li class='date-separator'><span>{$display_date}</span></li>";
            }
            
            $messageClass = $message['expediteur_id'] === $prefixed_user_id ? 'sent' : 'received';
            echo "<li class='message {$messageClass} " . ($message['statut'] === 'non_lu' ? 'unread' : '') . "'>";
            echo '<div class="message-header">';
            echo '<span class="sender"><i class="fas fa-user"></i> ' . htmlspecialchars($message['expediteur_nom']) . '</span>';
            echo '<span class="date"><i class="far fa-clock"></i> ' . date('d/m/Y H:i', strtotime($message['date_envoi'])) . '</span>';
            echo '</div>';
            echo '<div class="message-body">' . nl2br(htmlspecialchars($message['contenu'])) . '</div>';
            echo '</li>';
        }
        echo '</ul></div>';
    }

    // Update message status to 'lu'
    if ($role === 'etudiant') {
        $update_query = "UPDATE messages 
                         SET statut = 'lu' 
                         WHERE expediteur_id = CONCAT('C', :entreprise_id) 
                         AND destinataire_id = CONCAT('E', :user_id)
                         AND statut = 'non_lu'";
        $pdo->prepare($update_query)->execute([
            ':entreprise_id' => $selected_entreprise_id,
            ':user_id' => $user_id
        ]);
    } else {
        $update_query = "UPDATE messages 
                         SET statut = 'lu' 
                         WHERE expediteur_id = CONCAT('E', :etudiant_id) 
                         AND destinataire_id = CONCAT('C', :user_id)
                         AND statut = 'non_lu'";
        $pdo->prepare($update_query)->execute([
            ':etudiant_id' => $selected_etudiant_id,
            ':user_id' => $user_id
        ]);
    }
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>

<script>
// Dans le script existant, modifiez la fonction loadMessages
function loadMessages() {
    const entrepriseId = new URLSearchParams(window.location.search).get('entreprise_id');
    const etudiantId = new URLSearchParams(window.location.search).get('etudiant_id');
    
    $.ajax({
        url: 'load_messages.php',
        method: 'GET',
        data: { 
            entreprise_id: entrepriseId,
            etudiant_id: etudiantId,
            role: '<?php echo $role; ?>'
        },
        success: function(response) {
            $('#messageContent').html(response);
            scrollToBottom();
            
            // Afficher la section de réponse uniquement pour les conversations actives
            if ((etudiantId && '<?php echo $role; ?>' === 'entreprise') || 
                (entrepriseId && '<?php echo $role; ?>' === 'etudiant')) {
                $('.reply-section').show();
                initializeMessageForm();
            } else {
                $('.reply-section').hide();
            }
        }
    });
}
</script>