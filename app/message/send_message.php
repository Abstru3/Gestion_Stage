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

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$destinataire_id = isset($_POST['destinataire_id']) ? intval($_POST['destinataire_id']) : null;
$contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';

// Validation des données
if (!$destinataire_id || !$contenu) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

try {
    // Définir les préfixes en fonction du rôle
    if ($role === 'etudiant') {
        $expediteur_prefix = 'E';
        $destinataire_prefix = 'C';
    } else {
        $expediteur_prefix = 'C';
        $destinataire_prefix = 'E';
    }

    // Ajouter les préfixes aux IDs
    $prefixed_expediteur = $expediteur_prefix . $user_id;
    $prefixed_destinataire = $destinataire_prefix . $destinataire_id;

    // Vérifier si une conversation existe déjà
    $check_query = "SELECT MIN(conversation_id) as conv_id 
                   FROM messages 
                   WHERE (expediteur_id = :exp_id AND destinataire_id = :dest_id)
                   OR (expediteur_id = :dest_id AND destinataire_id = :exp_id)";
    
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute([
        ':exp_id' => $prefixed_expediteur,
        ':dest_id' => $prefixed_destinataire
    ]);
    $result = $check_stmt->fetch();
    
    $conversation_id = $result['conv_id'];
    if (!$conversation_id) {
        // Nouvelle conversation
        $max_conv_query = "SELECT COALESCE(MAX(conversation_id), 0) + 1 as next_id FROM messages";
        $max_conv_stmt = $pdo->prepare($max_conv_query);
        $max_conv_stmt->execute();
        $conversation_id = $max_conv_stmt->fetch()['next_id'];
    }

    // Insérer le message avec les IDs préfixés
    $query = "INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, statut, conversation_id) 
              VALUES (:expediteur_id, :destinataire_id, :contenu, NOW(), 'non_lu', :conversation_id)";

    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        ':expediteur_id' => $prefixed_expediteur,
        ':destinataire_id' => $prefixed_destinataire,
        ':contenu' => $contenu,
        ':conversation_id' => $conversation_id
    ]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}