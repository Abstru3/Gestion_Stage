<?php

// Fonction de connexion
function login($pdo, $identifier, $password) {
    // Vérifier d'abord dans la table etudiants
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE (email = :identifier OR username = :identifier)");
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Si pas trouvé dans etudiants, vérifier dans entreprises
        $stmt = $pdo->prepare("SELECT * FROM entreprises WHERE (email = :identifier OR username = :identifier)");
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

// Fonction d'inscription
function register($pdo, $username, $password, $email, $role) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $hashed_password, $email, $role]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fonction pour récupérer un utilisateur par son ID
function get_user($pdo, $user_id, $table) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fonction pour récupérer toutes les offres de stages
function get_internships($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT o.*, e.nom AS nom_entreprise 
            FROM offres_stages o
            JOIN entreprises e ON o.entreprise_id = e.id
            ORDER BY o.date_debut DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fonction pour calculer la durée entre deux dates
function calculateDuration($dateDebut, $dateFin) {
    if (!$dateDebut || !$dateFin) {
        return 'Non spécifiée';
    }
    
    $debut = new DateTime($dateDebut);
    $fin = new DateTime($dateFin);
    $interval = $debut->diff($fin);
    
    $mois = $interval->m + ($interval->y * 12);
    $jours = $interval->d;
    
    if ($mois > 0) {
        return $mois . ' mois' . ($jours > 0 ? ' et ' . $jours . ' jours' : '');
    }
    return $jours . ' jours';
}

// Fonction pour récupérer les candidatures d'un étudiant
function get_applications($pdo, $etudiant_id) {
    try {
        $stmt = $pdo->prepare("SELECT c.*, o.titre FROM candidatures c JOIN offres_stages o ON c.offre_id = o.id WHERE c.etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fonction pour ajouter une offre de stage
function post_internship($pdo, $entreprise_id, $titre, $description, $date_debut, $date_fin) {
    try {
        $stmt = $pdo->prepare("INSERT INTO offres_stages (entreprise_id, titre, description, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$entreprise_id, $titre, $description, $date_debut, $date_fin]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fonction pour récupérer un utilisateur par son email (optionnel)
function get_user_by_email($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

?>
