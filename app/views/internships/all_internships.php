<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

try {
    $search = $_GET['search'] ?? '';
    $mode_stage = $_GET['mode_stage'] ?? '';
    $sort = $_GET['sort'] ?? 'date_debut';
    $order = $_GET['order'] ?? 'ASC';
    $remuneration_min = $_GET['remuneration_min'] ?? '';
    $date_debut = $_GET['date_debut'] ?? '';
    $domaine = $_GET['domaine'] ?? '';
    $duree_min = $_GET['duree_min'] ?? '';
    $type_offre = $_GET['type_offre'] ?? '';

    $where_conditions = [];
    $params = [];

    if (!empty($search)) {
        $where_conditions[] = "(o.titre LIKE :search OR o.description LIKE :search OR e.nom LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($mode_stage)) {
        if ($mode_stage === 'hybride') {
            $where_conditions[] = "(o.mode_stage IN ('presentiel', 'distanciel'))";
        } else {
            $where_conditions[] = "o.mode_stage = :mode_stage";
            $params[':mode_stage'] = $mode_stage;
        }
    }

    if (!empty($remuneration_min)) {
        $where_conditions[] = "o.remuneration >= :remuneration_min";
        $params[':remuneration_min'] = $remuneration_min;
    }

    if (!empty($date_debut)) {
        $where_conditions[] = "o.date_debut >= :date_debut";
        $params[':date_debut'] = $date_debut;
    }

    if (!empty($domaine)) {
        $where_conditions[] = "o.domaine = :domaine";
        $params[':domaine'] = $domaine;
    }

    if (!empty($duree_min)) {
        $where_conditions[] = "DATEDIFF(o.date_fin, o.date_debut) >= :duree_min * 30";
        $params[':duree_min'] = $duree_min;
    }

    if (!empty($type_offre)) {
        $where_conditions[] = "o.type_offre = :type_offre";
        $params[':type_offre'] = $type_offre;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    $query = "SELECT o.*, e.nom as entreprise_nom, e.certification as entreprise_certification, 
              o.date_debut, o.date_fin, o.type_offre, o.type_remuneration
              FROM offres_stages o 
              JOIN entreprises e ON o.entreprise_id = e.id 
              $where_clause 
              ORDER BY e.certification DESC, $sort $order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $internships = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur SQL : " . $e->getMessage());
    $internships = [];
    $error_message = "Une erreur est survenue lors de la recherche.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Toutes les offres de stage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_all_internships.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <header>
        <div class="container-all-internships">
            <h1>Toutes les offres de stage</h1>
        </div>
    </header>

    <main class="container-all-internships">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="search-container">
            <form action="" method="get" class="search-form">
                <div class="search-wrapper">
                    <div class="search-bar">
                        <input type="text" 
                               name="search" 
                               id="search"
                               placeholder="Rechercher un stage ou une entreprise..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="button" id="filterToggle" class="filter-toggle">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </div>

                    <div id="filterTags" class="filter-tags"></div>

                    <div class="filters-panel" id="filtersPanel">
                        <h3 class="filter-section-title">Affiner votre recherche</h3>
                        
                        <!-- Mettre en avant le filtre type d'offre -->
                        <div class="filter-section highlighted-section">
                            <h4>Type d'offre</h4>
                            <div class="filter-type-buttons">
                                <button type="button" class="type-btn <?php echo !isset($_GET['type_offre']) || $_GET['type_offre'] === '' ? 'active' : ''; ?>" 
                                        data-value="">Tous</button>
                                <button type="button" class="type-btn stage-btn <?php echo isset($_GET['type_offre']) && $_GET['type_offre'] === 'stage' ? 'active' : ''; ?>" 
                                        data-value="stage">Stages</button>
                                <button type="button" class="type-btn alternance-btn <?php echo isset($_GET['type_offre']) && $_GET['type_offre'] === 'alternance' ? 'active' : ''; ?>" 
                                        data-value="alternance">Alternances</button>
                                <input type="hidden" name="type_offre" id="type_offre" value="<?php echo htmlspecialchars($type_offre); ?>">
                            </div>
                        </div>
                        
                        <div class="filters-grid">
                            <div class="filter-section">
                                <h4>Modalités</h4>
                                <div class="filter-group">
                                    <label for="mode_stage">Mode de travail</label>
                                    <select name="mode_stage" id="mode_stage">
                                        <option value="">Tous les modes</option>
                                        <option value="presentiel" <?php echo $mode_stage === 'presentiel' ? 'selected' : ''; ?>>Présentiel</option>
                                        <option value="distanciel" <?php echo $mode_stage === 'distanciel' ? 'selected' : ''; ?>>Distanciel</option>
                                        <option value="hybride" <?php echo $mode_stage === 'hybride' ? 'selected' : ''; ?>>Hybride</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" name="date_debut" id="date_debut" value="<?php echo htmlspecialchars($date_debut); ?>">
                                </div>
                            </div>
                            
                            <div class="filter-section">
                                <h4>Domaine et Rémunération</h4>
                                <div class="filter-group">
                                    <label for="domaine">Secteur d'activité</label>
                                    <select name="domaine" id="domaine">
                                        <option value="">Tous les domaines</option>
                                        <optgroup label="Informatique">
                                            <option value="developpement_web" <?php echo $domaine === 'developpement_web' ? 'selected' : ''; ?>>Développement Web</option>
                                            <option value="developpement_mobile" <?php echo $domaine === 'developpement_mobile' ? 'selected' : ''; ?>>Développement Mobile</option>
                                            <option value="reseaux" <?php echo $domaine === 'reseaux' ? 'selected' : ''; ?>>Réseaux</option>
                                            <option value="cybersecurite" <?php echo $domaine === 'cybersecurite' ? 'selected' : ''; ?>>Cybersécurité</option>
                                            <option value="data" <?php echo $domaine === 'data' ? 'selected' : ''; ?>>Data/IA</option>
                                        </optgroup>
                                        <optgroup label="Commerce">
                                            <option value="marketing_digital" <?php echo $domaine === 'marketing_digital' ? 'selected' : ''; ?>>Marketing Digital</option>
                                            <option value="commerce_international" <?php echo $domaine === 'commerce_international' ? 'selected' : ''; ?>>Commerce International</option>
                                            <option value="vente" <?php echo $domaine === 'vente' ? 'selected' : ''; ?>>Vente</option>
                                        </optgroup>
                                        <optgroup label="Autres">
                                            <option value="finance" <?php echo $domaine === 'finance' ? 'selected' : ''; ?>>Finance</option>
                                            <option value="ressources_humaines" <?php echo $domaine === 'ressources_humaines' ? 'selected' : ''; ?>>Ressources Humaines</option>
                                            <option value="communication" <?php echo $domaine === 'communication' ? 'selected' : ''; ?>>Communication</option>
                                            <option value="logistique" <?php echo $domaine === 'logistique' ? 'selected' : ''; ?>>Logistique</option>
                                            <option value="autre" <?php echo $domaine === 'autre' ? 'selected' : ''; ?>>Autre</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="remuneration_min">Rémunération minimale</label>
                                    <div class="input-with-icon">
                                        <input type="number" 
                                               name="remuneration_min"
                                               id="remuneration_min"
                                               placeholder="Ex: 600"
                                               min="0"
                                               value="<?php echo htmlspecialchars($remuneration_min); ?>">
                                        <span class="input-icon">€</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="reset" class="btn-reset">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn-apply">
                                <i class="fas fa-search"></i> Appliquer les filtres
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Onglets pour filtrer par type d'offre -->
        <div class="type-filter-tabs">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['type_offre' => ''])); ?>" 
               class="tab <?php echo !isset($_GET['type_offre']) || $_GET['type_offre'] === '' ? 'active' : ''; ?>">
                Toutes les offres
            </a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['type_offre' => 'stage'])); ?>" 
               class="tab stage-tab <?php echo isset($_GET['type_offre']) && $_GET['type_offre'] === 'stage' ? 'active' : ''; ?>">
                Stages
            </a>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['type_offre' => 'alternance'])); ?>" 
               class="tab alternance-tab <?php echo isset($_GET['type_offre']) && $_GET['type_offre'] === 'alternance' ? 'active' : ''; ?>">
                Alternances
            </a>
        </div>

        <h2>
            <?php if ($type_offre === 'stage'): ?>
                Stages disponibles
            <?php elseif ($type_offre === 'alternance'): ?>
                Alternances disponibles
            <?php else: ?>
                Offres disponibles
            <?php endif; ?>
        </h2>
        <div class="offers-grid">
            <?php if (empty($internships)): ?>
                <p class="no-results">Aucun stage ne correspond à vos critères de recherche.</p>
            <?php else: ?>
                <?php foreach ($internships as $internship): ?>
                    <div class="offer-card <?php 
                        $classes = [];
                        if ($internship['entreprise_certification']) {
                            $classes[] = 'certified-company';
                        }
                        if ($internship['type_offre'] === 'alternance') {
                            $classes[] = 'alternance-card';
                        }
                        echo implode(' ', $classes);
                    ?>">
                        <div class="offer-header">
                            <div class="mode-badge">
                                <i class="fas <?php echo $internship['mode_stage'] === 'distanciel' ? 'fa-laptop-house' : 'fa-building'; ?>"></i>
                                <?php echo htmlspecialchars($internship['mode_stage']); ?>
                            </div>
                            <div class="offer-header-content">
                                <div>
                                    <h3>
                                        <?php echo htmlspecialchars($internship['titre']); ?>
                                        <span class="offer-type-badge <?php echo $internship['type_offre']; ?>">
                                            <?php echo $internship['type_offre'] === 'alternance' ? 'Alternance' : 'Stage'; ?>
                                        </span>
                                    </h3>
                                    <span class="company-name">
                                        <?php echo htmlspecialchars($internship['entreprise_nom'] ?? 'Entreprise inconnue'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="offer-body">
                            <p><?php echo htmlspecialchars(substr($internship['description'], 0, 75)) . '...'; ?></p>
                            <div class="offer-details">
                                <span><i class="fas fa-calendar-alt"></i> Début: <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> Lieu: <?php echo htmlspecialchars($internship['lieu']); ?></span>
                                <span><i class="fas fa-clock"></i> Durée: <?php 
                                    if ($internship['date_fin'] && $internship['date_debut']) {
                                        echo calculateDuration($internship['date_debut'], $internship['date_fin']); 
                                    } else {
                                        echo 'Non spécifiée';
                                    }
                                ?></span>
                                <span><i class="fas fa-euro-sign"></i> Rémunération: <?php echo $internship['remuneration'] ? number_format($internship['remuneration'], 2, ',', ' ') . ' €' : 'Non spécifiée'; ?></span>
                            </div>
                        </div>
                        <div class="offer-footer">
                            <a href="/Gestion_Stage/app/views/internships/stage_details.php?id=<?php echo $internship['id']; ?>" class="btn btn-details">Voir plus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <p><a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a></p>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterToggle = document.getElementById('filterToggle');
        const filtersPanel = document.getElementById('filtersPanel');
        const filterTags = document.getElementById('filterTags');
        const searchForm = document.querySelector('.search-form');
        
        function submitSearch() {
            searchForm.submit();
        }

        const searchInput = document.getElementById('search');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(submitSearch, 500);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitSearch();
            }
        });

        filterToggle.addEventListener('click', () => {
            filtersPanel.classList.toggle('active');
        });

        function getFilterLabel(input) {
            if (input.tagName === 'SELECT') {
                const option = input.options[input.selectedIndex];
                return option ? option.text : '';
            }
            switch(input.id) {
                case 'remuneration_min':
                    return `${input.value}€ min`;
                case 'date_debut':
                    return `À partir du ${new Date(input.value).toLocaleDateString()}`;
                default:
                    return input.value;
            }
        }

        function updateFilterTags() {
            const activeFilters = [];
            const inputs = filtersPanel.querySelectorAll('input:not([value=""]), select:not([value=""])');
            
            inputs.forEach(input => {
                if (input.value) {
                    activeFilters.push(`
                        <span class="filter-tag">
                            ${getFilterLabel(input)}
                            <i class="fas fa-times" data-input-id="${input.id}"></i>
                        </span>
                    `);
                }
            });

            filterTags.innerHTML = activeFilters.join('');
            
            filterTags.querySelectorAll('.fa-times').forEach(icon => {
                icon.addEventListener('click', (e) => {
                    e.preventDefault();
                    const inputId = icon.dataset.inputId;
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.value = '';
                        if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        }
                        submitSearch();
                    }
                });
            });
        }

        filtersPanel.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT' || e.target.type === 'date' || e.target.type === 'number') {
                submitSearch();
            }
        });

        document.querySelector('.btn-reset').addEventListener('click', (e) => {
            e.preventDefault();
            searchForm.reset();
            updateFilterTags();
            submitSearch();
        });

        const offers = document.querySelectorAll('.offer-card');
        offers.forEach((offer, index) => {
            setTimeout(() => {
                offer.classList.add('visible');
            }, index * 100);
        });

        updateFilterTags();

        // Gestion des boutons de type d'offre
        const typeButtons = document.querySelectorAll('.type-btn');
        const typeInput = document.getElementById('type_offre');

        typeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Désactiver tous les boutons
                typeButtons.forEach(b => b.classList.remove('active'));
                // Activer le bouton cliqué
                this.classList.add('active');
                // Mettre à jour l'input caché
                typeInput.value = this.dataset.value;
            });
        });
    });
    </script>
</body>
</html>