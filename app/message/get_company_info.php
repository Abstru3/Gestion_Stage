<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

if (isset($_GET['entreprise_id'])) {
    $entreprise_id = intval($_GET['entreprise_id']);
    
    $query = "SELECT * FROM entreprises WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $entreprise_id]);
    $entreprise = $stmt->fetch();

    if ($entreprise): ?>
        <?php if ($entreprise['icone']): ?>
            <img src="/Gestion_Stage/public/uploads/profil/<?php echo htmlspecialchars($entreprise['icone']); ?>" 
                 alt="Logo <?php echo htmlspecialchars($entreprise['nom']); ?>">
        <?php else: ?>
            <i class="fas fa-building"></i>
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($entreprise['nom']); ?></h3>
        <a href="/Gestion_Stage/app/views/company_profile.php?id=<?php echo $entreprise_id; ?>" class="btn-profile">
            <i class="fas fa-building"></i> Accéder au profil
        </a>
    <?php endif;
}
?>