<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';

$verified_companies = $pdo->query("
    SELECT id, nom, icone, description 
    FROM entreprises 
    WHERE valide = 1 
    ORDER BY RAND() 
    LIMIT 4
")->fetchAll();

foreach ($verified_companies as $company): ?>
    <div class="company-card">
        <div class="company-logo">
            <?php if (!empty($company['icone'])): ?>
                <img src="/Gestion_Stage/public/uploads/profil/<?= htmlspecialchars($company['icone']) ?>" 
                     alt="Logo <?= htmlspecialchars($company['nom']) ?>">
            <?php else: ?>
                <i class="fas fa-building"></i>
            <?php endif; ?>
        </div>
        <h3><?= htmlspecialchars($company['nom']) ?></h3>
        <p><?= htmlspecialchars(substr($company['description'], 0, 100)) . '...' ?></p>
        <a href="/Gestion_Stage/app/views/company_profile.php?id=<?= $company['id'] ?>" 
           class="btn btn-company">
            Voir le profil
        </a>
    </div>
<?php endforeach; ?>