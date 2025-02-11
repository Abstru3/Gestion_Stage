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
        echo "<p class='empty-message'><i class='fas fa-envelope-open'></i> No messages in this conversation.</p>";
    } else {
        echo '<ul class="messages">';
        foreach ($messages as $message) {
            $messageClass = $message['expediteur_id'] === $prefixed_user_id ? 'sent' : 'received';
            echo '<li class="message ' . $messageClass . ' ' . ($message['statut'] === 'non_lu' ? 'unread' : '') . '">';
            echo '<div class="message-header">';
            echo '<span class="sender"><i class="fas fa-user"></i> ' . htmlspecialchars($message['expediteur_nom']) . '</span>';
            echo '<span class="date"><i class="far fa-clock"></i> ' . date('d/m/Y H:i', strtotime($message['date_envoi'])) . '</span>';
            echo '</div>';
            echo '<div class="message-body">' . nl2br(htmlspecialchars($message['contenu'])) . '</div>';
            echo '</li>';
        }
        echo '</ul>';
    }
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>

<script>
// Remplacer la fonction loadMessages dans la section <script>
function loadMessages() {
    const etudiantId = $('#etudiant_id').val();
    const entrepriseId = new URLSearchParams(window.location.search).get('entreprise_id');
    
    if ((etudiantId && '<?php echo $role; ?>' === 'entreprise') || 
        (entrepriseId && '<?php echo $role; ?>' === 'etudiant')) {
        $.ajax({
            url: 'load_messages.php',
            method: 'GET',
            data: { 
                etudiant_id: etudiantId,
                entreprise_id: entrepriseId
            },
            success: function(response) {
                $('#messageContent').html(response);
                scrollToBottom();
            }
        });
    }
}
</script>