<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /Gestion_Stage/app/views/panels/admin_panel.php');
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM entreprises WHERE id = ?");
$stmt->execute([$id]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$returnPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/Gestion_Stage/index.php';

if (!$entreprise) {
    header('Location: /Gestion_Stage/app/views/panels/admin_panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?= htmlspecialchars($entreprise['nom']) ?> - NeversStage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_company_profile.css">
    <link rel="icon" type="image/png" href="/Gestion_Stage/public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="company-profile-container">
        
        <h1 class="profile-title">
            Profil de <?= htmlspecialchars($entreprise['nom']) ?>
            <span class="validation-status <?= $entreprise['valide'] ? 'status-validated' : 'status-pending' ?>">
                <i class="fas <?= $entreprise['valide'] ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                <?= $entreprise['valide'] ? 'Entreprise validée' : 'En attente de validation' ?>
            </span>
        </h1>

        <div class="company-logo-container">
            <?php if (!empty($entreprise['icone'])): ?>
                <img src="/Gestion_Stage/public/uploads/profil/<?= htmlspecialchars($entreprise['icone']) ?>" 
                     alt="Icône <?= htmlspecialchars($entreprise['nom']) ?>" 
                     class="company-logo">
            <?php else: ?>
                <div class="default-icon">
                    <i class="fas fa-building profile-icon"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-building"></i>
                    <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-fingerprint"></i> SIRET</div>
                        <div><?= htmlspecialchars($entreprise['siret']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-industry"></i> Secteur d'activité</div>
                        <div><?= htmlspecialchars($entreprise['secteur_activite'] ?? 'Non renseigné') ?></div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>Description</h2>
                </div>
                <div><?= nl2br(htmlspecialchars($entreprise['description'])) ?></div>
            </div>

            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-address-card"></i>
                    <h2>Contact</h2>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> Nom du contact</div>
                        <div><?= htmlspecialchars($entreprise['nom_contact']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                        <div><?= htmlspecialchars($entreprise['email']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone"></i> Téléphone</div>
                        <div><?= htmlspecialchars($entreprise['telephone']) ?></div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2>Localisation</h2>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <div class="info-label">Adresse</div>
                        <div><?= htmlspecialchars($entreprise['adresse'] ?? 'Non renseignée') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ville</div>
                        <div><?= $entreprise['code_postal'] && $entreprise['ville'] ? 
                            htmlspecialchars($entreprise['code_postal']) . ' ' . htmlspecialchars($entreprise['ville']) : 
                            'Non renseignée' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pays</div>
                        <div><?= htmlspecialchars($entreprise['pays'] ?? 'Non renseigné') ?></div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-plus-circle"></i>
                    <h2>Informations complémentaires</h2>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <div class="info-label">Site web</div>
                        <?php if ($entreprise['site_web']): ?>
                            <a href="<?= htmlspecialchars($entreprise['site_web']) ?>" target="_blank">
                                <?= htmlspecialchars($entreprise['site_web']) ?>
                            </a>
                        <?php else: ?>
                            Non renseigné
                        <?php endif; ?>
                    </div>
                    <div class="info-item">
                        <div class="info-label">TVA Intracommunautaire</div>
                        <div><?= htmlspecialchars($entreprise['tva_intracommunautaire'] ?? 'Non renseigné') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="return-button">
            <?php if ($isAdmin): ?>
                <a href="/Gestion_Stage/app/views/panels/admin_panel.php" class="index-button">
                    <i class="fas fa-arrow-left"></i> Retour au panel admin
                </a>
            <?php else: ?>
                <a href="<?php echo $returnPage; ?>" class="index-button">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>