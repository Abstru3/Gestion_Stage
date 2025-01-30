<?php
function login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function register($pdo, $username, $password, $email, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, $hashed_password, $email, $role]);
}

function get_user($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function get_internships($pdo) {
    $stmt = $pdo->query("SELECT * FROM offres_stages ORDER BY date_debut DESC");
    return $stmt->fetchAll();
}

function get_applications($pdo, $etudiant_id) {
    $stmt = $pdo->prepare("SELECT c.*, o.titre FROM candidatures c JOIN offres_stages o ON c.offre_id = o.id WHERE c.etudiant_id = ?");
    $stmt->execute([$etudiant_id]);
    return $stmt->fetchAll();
}

function post_internship($pdo, $entreprise_id, $titre, $description, $date_debut, $date_fin) {
    $stmt = $pdo->prepare("INSERT INTO offres_stages (entreprise_id, titre, description, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$entreprise_id, $titre, $description, $date_debut, $date_fin]);
}

// Ajouter d'autres fonctions utilitaires selon les besoins
?>