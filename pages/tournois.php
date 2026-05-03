<?php
require_once __DIR__ . '/../assets/php/config/db.php';

$fJeu    = trim($_GET['jeu']    ?? '');
$fStatut = trim($_GET['statut'] ?? '');
$fPlaces = trim($_GET['places'] ?? '');
$page    = max(1, (int) ($_GET['page'] ?? 1));
$parPage = 6;

$jeuxAutorises    = ['lol', 'valorant', 'cs2', 'fortnite', 'rocket-league'];
$statutsAutorises = ['a-venir', 'en-cours', 'termine'];
$placesAutorisees = ['dispo', 'complet'];

if (!in_array($fJeu,    $jeuxAutorises,    true)) $fJeu    = '';
if (!in_array($fStatut, $statutsAutorises, true)) $fStatut = '';
if (!in_array($fPlaces, $placesAutorisees, true)) $fPlaces = '';

$tournois  = [];
$totalCount = 0;
$dbError   = '';

try {
    // Construction du WHERE
    $where  = 'WHERE 1=1';
    $params = [];

    if ($fJeu !== '') {
        $where .= ' AND t.jeu = :jeu';
        $params[':jeu'] = $fJeu;
    }
    if ($fStatut !== '') {
        $where .= ' AND t.statut = :statut';
        $params[':statut'] = $fStatut;
    }

    $havingClause = '';
    if ($fPlaces === 'dispo') {
        $havingClause = 'HAVING COALESCE(rc.inscrits, 0) < t.nb_places';
    } elseif ($fPlaces === 'complet') {
        $havingClause = 'HAVING COALESCE(rc.inscrits, 0) >= t.nb_places';
    }

    $orderClause = "ORDER BY CASE t.statut WHEN 'en-cours' THEN 1 WHEN 'a-venir' THEN 2 WHEN 'termine' THEN 3 ELSE 4 END, t.date_debut ASC";

    // Requête pour compter le total
    $sqlCount = "SELECT COUNT(*) FROM (
                    SELECT t.id FROM tournois t
                    LEFT JOIN (
                        SELECT tournoi_id, COUNT(*) AS inscrits
                        FROM reservations WHERE statut <> 'annulee' GROUP BY tournoi_id
                    ) rc ON rc.tournoi_id = t.id
                    $where
                    GROUP BY t.id
                    $havingClause
                 ) AS sub";
    
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $totalCount = (int) $stmtCount->fetchColumn();
    $totalPages = max(1, ceil($totalCount / $parPage));
    
    if ($page > $totalPages) $page = $totalPages;
    $offset = ($page - 1) * $parPage;

    // Chargement des tournois paginés
    $sql = "SELECT
                t.id, t.nom, t.jeu, t.image, t.date_debut, t.lieu,
                t.nb_places, t.cashprize, t.description, t.statut,
                COALESCE(rc.inscrits, 0) AS equipes_inscrites
            FROM tournois t
            LEFT JOIN (
                SELECT tournoi_id, COUNT(*) AS inscrits
                FROM reservations WHERE statut <> 'annulee' GROUP BY tournoi_id
            ) rc ON rc.tournoi_id = t.id
            $where
            GROUP BY t.id
            $havingClause
            $orderClause
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $parPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tournois = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('[TOURNOIS LIST] ' . $e->getMessage());
    $dbError = 'Impossible de charger les tournois pour le moment.';
}

$jeuLabels = [
    'lol'          => 'League of Legends',
    'valorant'     => 'Valorant',
    'cs2'          => 'Counter-Strike 2',
    'fortnite'     => 'Fortnite',
    'rocket-league'=> 'Rocket League',
];

$jeuImages = [
    'lol'          => '../img/lol_cover.webp',
    'valorant'     => '../img/fc25.webp',
    'cs2'          => '../img/cs2.webp',
    'fortnite'     => '../img/ssbu.webp',
    'rocket-league'=> '../img/rocket_league.webp',
];

$statutLabels = [
    'a-venir'  => 'À venir',
    'en-cours' => 'En cours',
    'termine'  => 'Terminé',
];

// Construction de l'URL de base pour la pagination (sans ?page=)
$queryParamsPagination = [];
if ($fJeu !== '')    $queryParamsPagination['jeu']    = $fJeu;
if ($fStatut !== '') $queryParamsPagination['statut'] = $fStatut;
if ($fPlaces !== '') $queryParamsPagination['places'] = $fPlaces;

function paginationUrl(array $baseParams, int $targetPage): string {
    $p = array_merge($baseParams, ['page' => $targetPage]);
    return 'tournois.php?' . http_build_query($p);
}

$rootPath          = '../';
$pageTitle         = 'Tournois - Gaming Campus';
$metaDescription   = 'Catalogue de tous les tournois gaming du campus. Consultez les jeux, dates, places disponibles et inscrivez votre équipe.';
$cssSpecifique     = 'tournois.css';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <main id="main-content">

        <section class="page-hero" aria-label="En-tête de la page Tournois">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Tournois</li>
                    </ol>
                </nav>
                <h1>🎮 Catalogue des Tournois</h1>
                <p class="page-description">Découvre tous les tournois organisés sur le campus. Choisis ton jeu, vérifie les places disponibles et inscris ton équipe !</p>
            </div>
        </section>

        <section class="filters-section" aria-label="Filtres de recherche">
            <div class="section-container">
                <form class="filters-form" method="get" action="tournois.php">
                    <fieldset>
                        <legend>Filtrer les tournois</legend>

                        <div class="filter-group">
                            <label for="filter-jeu">Jeu</label>
                            <select id="filter-jeu" name="jeu" onchange="this.form.submit()">
                                <option value="" <?= $fJeu === '' ? 'selected' : '' ?>>Tous les jeux</option>
                                <option value="lol"           <?= $fJeu === 'lol'           ? 'selected' : '' ?>>League of Legends</option>
                                <option value="valorant"      <?= $fJeu === 'valorant'      ? 'selected' : '' ?>>Valorant</option>
                                <option value="cs2"           <?= $fJeu === 'cs2'           ? 'selected' : '' ?>>Counter-Strike 2</option>
                                <option value="fortnite"      <?= $fJeu === 'fortnite'      ? 'selected' : '' ?>>Fortnite</option>
                                <option value="rocket-league" <?= $fJeu === 'rocket-league' ? 'selected' : '' ?>>Rocket League</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter-statut">Statut</label>
                            <select id="filter-statut" name="statut" onchange="this.form.submit()">
                                <option value="" <?= $fStatut === '' ? 'selected' : '' ?>>Tous les statuts</option>
                                <option value="a-venir"  <?= $fStatut === 'a-venir'  ? 'selected' : '' ?>>À venir</option>
                                <option value="en-cours" <?= $fStatut === 'en-cours' ? 'selected' : '' ?>>En cours</option>
                                <option value="termine"  <?= $fStatut === 'termine'  ? 'selected' : '' ?>>Terminé</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter-places">Places disponibles</label>
                            <select id="filter-places" name="places" onchange="this.form.submit()">
                                <option value="" <?= $fPlaces === '' ? 'selected' : '' ?>>Toutes</option>
                                <option value="dispo"   <?= $fPlaces === 'dispo'   ? 'selected' : '' ?>>Places disponibles</option>
                                <option value="complet" <?= $fPlaces === 'complet' ? 'selected' : '' ?>>Complet</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <?php if ($fJeu !== '' || $fStatut !== '' || $fPlaces !== ''): ?>
                        <a href="tournois.php" class="btn btn-outline">Réinitialiser</a>
                        <?php endif; ?>
                    </fieldset>
                </form>
            </div>
        </section>

        <section id="liste-tournois" aria-labelledby="titre-liste-tournois">
            <div class="section-container">
                <h2 id="titre-liste-tournois" class="sr-only">Liste des tournois</h2>

                <div class="results-summary">
                    <p id="results-summary-text">
                        <strong><?= $totalCount ?> tournoi<?= $totalCount > 1 ? 's' : '' ?></strong> trouvé<?= $totalCount > 1 ? 's' : '' ?>
                    </p>
                </div>

                <?php if ($dbError !== ''): ?>
                <div class="empty-state empty-state-full">
                    <span class="empty-state-icon">⚠️</span>
                    <p><?= htmlspecialchars($dbError) ?></p>
                </div>
                <?php elseif (!empty($tournois)): ?>

                <!-- Grille des tournois -->
                <div class="cards-grid">
                        <?php foreach ($tournois as $tournoi): ?>
                        <?php
                            $jeu         = (string) $tournoi['jeu'];
                            $statut      = (string) $tournoi['statut'];
                            $placesTotal = max(1, (int) $tournoi['nb_places']);
                            $inscrits    = (int) $tournoi['equipes_inscrites'];
                            $restantes   = max(0, $placesTotal - $inscrits);
                            $ratio       = min(100, (int) round(($inscrits / $placesTotal) * 100));
                            $dataPlaces  = $restantes > 0 ? 'dispo' : 'complet';
                            $cardImg = !empty($tournoi['image'])
                                ? '../' . htmlspecialchars($tournoi['image'])
                                : htmlspecialchars($jeuImages[$jeu] ?? '../img/lol_cover.webp');
                        ?>
                        <article class="card card-tournoi"
                                 data-jeu="<?= htmlspecialchars($jeu) ?>"
                                 data-statut="<?= htmlspecialchars($statut) ?>"
                                 data-places="<?= $dataPlaces ?>">
                            <div class="card-badge badge-<?= htmlspecialchars($statut) ?>"><?= htmlspecialchars($statutLabels[$statut] ?? $statut) ?></div>
                            <img src="<?= $cardImg ?>"
                                 alt="<?= htmlspecialchars($jeuLabels[$jeu] ?? $jeu) ?>" class="card-image" loading="lazy">
                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($tournoi['nom']) ?></h3>
                                <ul class="card-info-list">
                                    <li class="card-info">
                                        <span class="info-icon">🎮</span>
                                        <span><?= htmlspecialchars($jeuLabels[$jeu] ?? $jeu) ?></span>
                                    </li>
                                    <li class="card-info">
                                        <span class="info-icon">📅</span>
                                        <time datetime="<?= htmlspecialchars((string) $tournoi['date_debut']) ?>"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $tournoi['date_debut']))) ?></time>
                                    </li>
                                    <li class="card-info">
                                        <span class="info-icon">👥</span>
                                        <span><?= $inscrits ?> / <?= $placesTotal ?> équipes inscrites</span>
                                    </li>
                                    <li class="card-info">
                                        <span class="info-icon">🏆</span>
                                        <span><?= number_format((float) $tournoi['cashprize'], 0, ',', ' ') ?>€ cashprize</span>
                                    </li>
                                    <li class="card-info">
                                        <span class="info-icon">📍</span>
                                        <span><?= htmlspecialchars((string) ($tournoi['lieu'] ?: 'Campus')) ?></span>
                                    </li>
                                </ul>
                                <div class="card-places">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?= $ratio ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-fill" style="width: <?= $ratio ?>%"></div>
                                    </div>
                                    <?php if ($restantes > 0): ?>
                                    <span class="places-restantes"><?= $restantes ?> places restantes</span>
                                    <?php else: ?>
                                    <span class="places-restantes places-complet">Complet</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-actions">
                                <a href="tournoi-detail.php?id=<?= (int) $tournoi['id'] ?>" class="btn btn-outline btn-block">Voir le tournoi</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination" style="margin-top:2rem;display:flex;justify-content:center;gap:0.5rem;flex-wrap:wrap;">
                    <?php if ($page > 1): ?>
                    <a href="<?= paginationUrl($queryParamsPagination, $page - 1) ?>" class="btn btn-outline btn-sm">Précédent</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= paginationUrl($queryParamsPagination, $i) ?>" class="btn <?= $i === $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <a href="<?= paginationUrl($queryParamsPagination, $page + 1) ?>" class="btn btn-outline btn-sm">Suivant</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state empty-state-full">
                    <span class="empty-state-icon">🎮</span>
                    <p>Aucun tournoi disponible pour le moment.</p>
                    <p class="empty-state-sub">Le prochain tournoi sera annoncé très bientôt. Reviens vite !</p>
                    <a href="../index.php" class="btn btn-outline">Retour à l'accueil</a>
                </div>
                <?php endif; ?>

            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
