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
$destinataire_id = $_POST['destinataire_id'];
$contenu = $_POST['contenu'];

// Validation des données
if (empty($destinataire_id) || empty($contenu)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $query = "INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, statut) 
              VALUES (:expediteur_id, :destinataire_id, :contenu, NOW(), 'non_lu')";
    
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        ':expediteur_id' => $user_id,
        ':destinataire_id' => $destinataire_id,
        ':contenu' => $contenu
    ]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du message']);
}
?>