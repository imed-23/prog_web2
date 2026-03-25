<?php
require_once __DIR__ . '/../assets/php/config/db.php';

$search = trim($_GET['q'] ?? '');
$participants = [];
$dbError = '';

try {
    $sql = 'SELECT id, pseudo, prenom, nom, avatar, jeu_principal, role, created_at
            FROM utilisateurs
            WHERE role <> "admin"';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (pseudo LIKE :q OR prenom LIKE :q OR nom LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[PARTICIPANTS LIST] ' . $e->getMessage());
    $dbError = 'Impossible de charger les participants.';
}

$jeuLabels = [
    'lol' => 'League of Legends',
    'valorant' => 'Valorant',
    'cs2' => 'Counter-Strike 2',
    'fortnite' => 'Fortnite',
    'rocket-league' => 'Rocket League',
    'autre' => 'Autre',
];

$rootPath        = '../';
$pageTitle       = 'Participants - Gaming Campus';
$metaDescription = 'Liste de tous les participants inscrits sur la plateforme Gaming Campus.';
$cssSpecifique   = 'participants.css';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête participants">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Participants</li>
                    </ol>
                </nav>
                <h1>👥 Participants</h1>
                <p class="page-description">Retrouve tous les joueurs inscrits sur la plateforme et consulte leurs profils.</p>
            </div>
        </section>

        <!-- ======== SEARCH ======== -->
        <section class="search-section" aria-label="Recherche participants">
            <div class="section-container">
                <form class="search-form" method="get" action="participants.php" role="search">
                    <label for="search-participant" class="sr-only">Rechercher un joueur</label>
                    <input type="search" id="search-participant" name="q" placeholder="Rechercher un joueur par pseudo..." autocomplete="off" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>
        </section>

        <!-- ======== LISTE PARTICIPANTS ======== -->
        <section id="liste-participants" aria-labelledby="titre-participants">
            <div class="section-container">
                <h2 id="titre-participants">Joueurs inscrits</h2>

                <div class="cards-grid cards-grid-sm participants-grid">
                    <?php if ($dbError !== ''): ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">⚠️</span>
                        <p><?= htmlspecialchars($dbError) ?></p>
                    </div>
                    <?php elseif (!empty($participants)): ?>
                    <?php foreach ($participants as $participant): ?>
                    <article class="card card-joueur">
                        <div class="card-body">
                            <?php if (!empty($participant['avatar'])): ?>
                            <img class="joueur-avatar" src="../<?= htmlspecialchars($participant['avatar']) ?>" alt="Avatar de <?= htmlspecialchars($participant['pseudo']) ?>" loading="lazy">
                            <?php else: ?>
                            <img class="joueur-avatar" src="../img/lol_cover.webp" alt="Avatar par défaut" loading="lazy">
                            <?php endif; ?>
                            <h3 class="card-title"><?= htmlspecialchars($participant['pseudo']) ?></h3>
                            <p class="joueur-nom"><?= htmlspecialchars(trim(($participant['prenom'] ?? '') . ' ' . ($participant['nom'] ?? ''))) ?></p>
                            <p class="joueur-equipe">Rôle: <?= htmlspecialchars($participant['role']) ?></p>
                            <ul class="joueur-stats">
                                <li>🎮 Jeu principal: <strong><?= htmlspecialchars($jeuLabels[$participant['jeu_principal'] ?? ''] ?? 'Non renseigné') ?></strong></li>
                                <li>📅 Inscrit le: <strong><?= htmlspecialchars(date('d/m/Y', strtotime((string) $participant['created_at']))) ?></strong></li>
                            </ul>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">👥</span>
                        <p>Aucun participant pour le moment.</p>
                        <p class="empty-state-sub">Les joueurs inscrits apparaîtront ici.</p>
                        <a href="inscription.php" class="btn btn-primary">Rejoindre la communauté</a>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
