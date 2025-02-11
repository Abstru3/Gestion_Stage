<?php
header('Content-Type: application/json');
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];
$destinataire_id = isset($_POST['destinataire_id']) ? intval($_POST['destinataire_id']) : null;
$contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';

// Validation des données
if (!$destinataire_id || !$contenu) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

try {
    // Vérifier si une conversation existe déjà
    $check_query = "SELECT MIN(conversation_id) as conv_id 
                   FROM messages 
                   WHERE (expediteur_id = :user_id AND destinataire_id = :dest_id)
                   OR (expediteur_id = :dest_id AND destinataire_id = :user_id)";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute([
        ':user_id' => $user_id,
        ':dest_id' => $destinataire_id
    ]);
    $result = $check_stmt->fetch();
    
    $conversation_id = $result['conv_id'];
    if (!$conversation_id) {
        // Nouvelle conversation
        $max_conv_query = "SELECT MAX(conversation_id) as max_id FROM messages";
        $max_conv_stmt = $pdo->prepare($max_conv_query);
        $max_conv_stmt->execute();
        $max_result = $max_conv_stmt->fetch();
        $conversation_id = ($max_result['max_id'] ?? 0) + 1;
    }

    $query = "INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, statut, conversation_id) 
              VALUES (:expediteur_id, :destinataire_id, :contenu, NOW(), 'non_lu', :conversation_id)";
    
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        ':expediteur_id' => $user_id,
        ':destinataire_id' => $destinataire_id,
        ':contenu' => $contenu,
        ':conversation_id' => $conversation_id
    ]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>