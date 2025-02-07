<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $feedback = trim($_POST['feedback']);
    $user_name = trim($_POST['user_name']);
    $role = trim($_POST['role']);

    if (!empty($feedback) && !empty($user_name) && !empty($role)) {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_name, role, feedback, date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_name, $role, $feedback]);
        $_SESSION['success'] = "Avis envoyé. Merci de contribuer à l'amélioration de notre site web.";
    } else {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
    }
}

header("Location: /Gestion_Stage/app/views/home.php");
exit();
?>