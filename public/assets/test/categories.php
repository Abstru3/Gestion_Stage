<?php
require_once __DIR__ . '/SimplePdfAnalyzer.php';

$pdfFolder = __DIR__ . '/pdf_examples/';
if (!is_dir($pdfFolder)) {
    mkdir($pdfFolder, 0777, true);
}

$pdfs = glob($pdfFolder . "*.pdf");
$analyzer = new SimplePdfAnalyzer();
$allMatches = [];

// Analyse des PDF
foreach ($pdfs as $pdfFile) {
    $matches = $analyzer->analyzePdf($pdfFile);
    if ($matches) {
        foreach ($matches as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $files) {
                if (!isset($allMatches[$category])) {
                    $allMatches[$category] = [];
                }
                if (!isset($allMatches[$category][$subcategory])) {
                    $allMatches[$category][$subcategory] = [];
                }
                $allMatches[$category][$subcategory] = array_merge(
                    $allMatches[$category][$subcategory], 
                    $files
                );
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Page de test</title>
    <link rel="stylesheet" href="categories.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="categories-container">
        <h1>Catégories de Stage</h1>
        <div class="categories-grid">
            <!-- Développement -->
            <div class="category-card" data-category="dev">
                <i class="fas fa-code"></i>
                <h3>Développement</h3>
                <ul>
                    <li>
                        Développement Web
                        <?php if (!empty($allMatches['dev']['web'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['dev']['web'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Développement Mobile
                        <?php if (!empty($allMatches['dev']['mobile'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['dev']['mobile'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Développement Logiciel
                        <?php if (!empty($allMatches['dev']['logiciel'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['dev']['logiciel'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        DevOps
                        <?php if (!empty($allMatches['dev']['devops'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['dev']['devops'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

            <!-- Design -->
            <div class="category-card" data-category="design">
                <i class="fas fa-palette"></i>
                <h3>Design</h3>
                <ul>
                    <li>
                        UI/UX Design
                        <?php if (!empty($allMatches['design']['ui_ux'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['design']['ui_ux'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Design Graphique
                        <?php if (!empty($allMatches['design']['graphique'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['design']['graphique'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Motion Design
                        <?php if (!empty($allMatches['design']['motion'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['design']['motion'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Web Design
                        <?php if (!empty($allMatches['design']['web_design'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['design']['web_design'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

            <!-- Marketing -->
            <div class="category-card" data-category="marketing">
                <i class="fas fa-bullhorn"></i>
                <h3>Marketing</h3>
                <ul>
                    <li>Marketing Digital</li>
                    <li>Community Management</li>
                    <li>SEO/SEA</li>
                    <li>Growth Hacking</li>
                </ul>
            </div>

            <!-- Data -->
            <div class="category-card" data-category="data">
                <i class="fas fa-database"></i>
                <h3>Data</h3>
                <ul>
                    <li>
                        Data Analysis
                        <?php if (!empty($allMatches['data']['analyse'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['data']['analyse'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Data Science
                        <?php if (!empty($allMatches['data']['science'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['data']['science'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Big Data
                        <?php if (!empty($allMatches['data']['bigdata'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['data']['bigdata'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Business Intelligence
                        <?php if (!empty($allMatches['data']['bi'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['data']['bi'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

            <!-- Réseaux -->
            <div class="category-card" data-category="réseaux">
                <i class="fas fa-network-wired"></i>
                <h3>Réseaux</h3>
                <ul>
                    <li>
                        Administration Réseau
                        <?php if (!empty($allMatches['réseaux']['administration'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['réseaux']['administration'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Sécurité
                        <?php if (!empty($allMatches['réseaux']['sécurité'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['réseaux']['sécurité'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Cloud
                        <?php if (!empty($allMatches['réseaux']['cloud'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['réseaux']['cloud'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Systèmes
                        <?php if (!empty($allMatches['réseaux']['systèmes'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['réseaux']['systèmes'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

                        <!-- Management -->
                        <div class="category-card" data-category="gestion">
                <i class="fas fa-users"></i>
                <h3>Management</h3>
                <ul>
                    <li>
                        Gestion de Projet
                        <?php if (!empty($allMatches['gestion']['projet'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['gestion']['projet'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Qualité et Processus
                        <?php if (!empty($allMatches['gestion']['qualité'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['gestion']['qualité'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Finance et Gestion
                        <?php if (!empty($allMatches['gestion']['finances'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['gestion']['finances'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <li>
                        Ressources Humaines
                        <?php if (!empty($allMatches['gestion']['rh'])): ?>
                            <ul class="pdf-matches">
                                <?php foreach ($allMatches['gestion']['rh'] as $pdf): ?>
                                    <li class="pdf-item"><?php echo htmlspecialchars($pdf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>