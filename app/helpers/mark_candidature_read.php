<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidature_id'])) {
    $candidature_id = intval($_POST['candidature_id']);
    
    $stmt = $pdo->prepare("
        UPDATE candidatures 
        SET date_lecture = CURRENT_TIMESTAMP 
        WHERE id = :candidature_id 
        AND etudiant_id = :etudiant_id
    ");
    
    try {
        $stmt->execute([
            ':candidature_id' => $candidature_id,
            ':etudiant_id' => $_SESSION['user_id']
        ]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}