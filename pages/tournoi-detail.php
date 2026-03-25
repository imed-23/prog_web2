<?php
require_once __DIR__ . '/../assets/php/config/auth.php';
require_once __DIR__ . '/../assets/php/config/db.php';
gc_start_session();

$currentUser = gc_current_user();
$messageSucces = '';
$messageErreur = '';

$tournoiId = (int) ($_GET['id'] ?? 0);
$tournoi = null;
$equipes = [];
$equipesConfirmees = [];
$reservationExistante = null;
$dbError = '';

$jeuLabels = [
    'lol' => 'League of Legends',
    'valorant' => 'Valorant',
    'cs2' => 'Counter-Strike 2',
    'fortnite' => 'Fortnite',
    'rocket-league' => 'Rocket League',
];
$jeuImages = [
    'lol' => '../img/lol_cover.webp',
    'valorant' => '../img/fc25.webp',
    'cs2' => '../img/cs2.webp',
    'fortnite' => '../img/ssbu.webp',
    'rocket-league' => '../img/rocket_league.webp',
];
$statutLabels = [
    'a-venir' => 'À venir',
    'en-cours' => 'En cours',
    'termine' => 'Terminé',
];

$repartitionPrix = [
    1 => 55,
    2 => 30,
    3 => 15,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    } else {
        $action = trim($_POST['action'] ?? '');

        if ($action === 'become_capitaine') {
            if (!$currentUser) {
                header('Location: connexion.php?redirect=' . urlencode('tournoi-detail.php?id=' . $tournoiId));
                exit;
            }

            try {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET role = "capitaine" WHERE id = ? AND role = "visiteur"');
                $stmt->execute([(int) $currentUser['id']]);
                $_SESSION['user_role'] = 'capitaine';
                $currentUser = gc_current_user();
                $messageSucces = 'Ton compte est maintenant capitaine. Tu peux inscrire ton équipe.';
            } catch (PDOException $e) {
                error_log('[TOURNOI DETAIL ROLE] ' . $e->getMessage());
                $messageErreur = 'Impossible de mettre à jour ton rôle pour le moment.';
            }
        }

        if ($action === 'reserve') {
            if (!$currentUser) {
                header('Location: connexion.php?redirect=' . urlencode('tournoi-detail.php?id=' . $tournoiId));
                exit;
            }

            if (!in_array($currentUser['role'], ['capitaine', 'admin'], true)) {
                $messageErreur = 'Seuls les capitaines peuvent inscrire une équipe.';
            }

            $nomEquipe = trim($_POST['nom_equipe'] ?? '');
            if ($messageErreur === '' && (strlen($nomEquipe) < 2 || strlen($nomEquipe) > 50)) {
                $messageErreur = 'Le nom d\'équipe doit contenir entre 2 et 50 caractères.';
            }

            if ($messageErreur === '' && $tournoiId <= 0) {
                $messageErreur = 'Tournoi invalide.';
            }

            if ($messageErreur === '') {
                try {
                    $stmt = $pdo->prepare('SELECT nb_places FROM tournois WHERE id = ? LIMIT 1');
                    $stmt->execute([$tournoiId]);
                    $tournoiForReserve = $stmt->fetch();

                    if (!$tournoiForReserve) {
                        $messageErreur = 'Tournoi introuvable.';
                    } else {
                        $stmt = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE tournoi_id = ? AND statut <> "annulee"');
                        $stmt->execute([$tournoiId]);
                        $inscrits = (int) $stmt->fetchColumn();
                        $total = (int) $tournoiForReserve['nb_places'];

                        if ($inscrits >= $total) {
                            $messageErreur = 'Ce tournoi est complet.';
                        } else {
                            $stmt = $pdo->prepare('SELECT id FROM reservations WHERE tournoi_id = ? AND capitaine_id = ? LIMIT 1');
                            $stmt->execute([$tournoiId, (int) $currentUser['id']]);
                            if ($stmt->fetch()) {
                                $messageErreur = 'Tu as déjà une inscription pour ce tournoi.';
                            } else {
                                $stmt = $pdo->prepare('INSERT INTO reservations (tournoi_id, capitaine_id, nom_equipe, statut) VALUES (?, ?, ?, "en-attente")');
                                $stmt->execute([$tournoiId, (int) $currentUser['id'], $nomEquipe]);
                                $messageSucces = 'Inscription de ton équipe enregistrée.';
                            }
                        }
                    }
                } catch (PDOException $e) {
                    error_log('[TOURNOI DETAIL RESERVE] ' . $e->getMessage());
                    $messageErreur = 'Impossible d\'enregistrer la réservation pour le moment.';
                }
            }
        }
    }
}

if ($tournoiId > 0) {
    try {
        $stmt = $pdo->prepare('SELECT t.*, COALESCE(rc.inscrits, 0) AS equipes_inscrites
                               FROM tournois t
                               LEFT JOIN (
                                   SELECT tournoi_id, COUNT(*) AS inscrits
                                   FROM reservations
                                   WHERE statut <> "annulee"
                                   GROUP BY tournoi_id
                               ) rc ON rc.tournoi_id = t.id
                               WHERE t.id = ?
                               LIMIT 1');
        $stmt->execute([$tournoiId]);
        $tournoi = $stmt->fetch();

        if ($tournoi) {
            $stmt = $pdo->prepare('SELECT r.nom_equipe, r.statut, u.pseudo
                                   FROM reservations r
                                   INNER JOIN utilisateurs u ON u.id = r.capitaine_id
                                   WHERE r.tournoi_id = ?
                                   ORDER BY r.created_at ASC');
            $stmt->execute([$tournoiId]);
            $equipes = $stmt->fetchAll();

            foreach ($equipes as $equipe) {
                if (($equipe['statut'] ?? '') === 'confirmee') {
                    $equipesConfirmees[] = $equipe;
                }
            }

            if ($currentUser) {
                $stmt = $pdo->prepare('SELECT id, nom_equipe, statut FROM reservations WHERE tournoi_id = ? AND capitaine_id = ? LIMIT 1');
                $stmt->execute([$tournoiId, (int) $currentUser['id']]);
                $reservationExistante = $stmt->fetch() ?: null;
            }
        }
    } catch (PDOException $e) {
        error_log('[TOURNOI DETAIL] ' . $e->getMessage());
        $dbError = 'Impossible de charger les détails du tournoi.';
    }
}

$rootPath        = '../';
$pageTitle       = $tournoi ? ('Tournoi - ' . $tournoi['nom']) : 'Détail Tournoi - Gaming Campus';
$metaDescription = 'Informations détaillées sur ce tournoi : format, règles, cashprize et équipes inscrites.';
$cssSpecifique   = 'tournoi-detail.css';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête tournoi">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="tournois.php">Tournois</a></li>
                        <li aria-current="page">Détail du tournoi</li>
                    </ol>
                </nav>
                <h1>🎮 Détail du Tournoi</h1>
                <p class="page-description">Informations complètes sur ce tournoi.</p>
            </div>
        </section>

        <!-- ======== CONTENU TOURNOI ======== -->
        <section id="tournoi-detail" aria-labelledby="titre-tournoi">
            <div class="section-container">
                <?php if ($messageSucces !== ''): ?>
                <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
                <?php endif; ?>
                <?php if ($messageErreur !== ''): ?>
                <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
                <?php endif; ?>

                <div class="tournoi-detail-grid">
                    <?php if ($dbError !== ''): ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">⚠️</span>
                        <p><?= htmlspecialchars($dbError) ?></p>
                    </div>
                    <?php elseif (!$tournoi): ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">🎮</span>
                        <p>Tournoi introuvable.</p>
                        <p class="empty-state-sub"><a href="tournois.php">Retour à la liste des tournois</a></p>
                    </div>
                    <?php else: ?>

                    <!-- Colonne principale -->
                    <div class="tournoi-main">

                        <!-- Bannière -->
                        <div class="tournoi-banner">
                            <img src="<?= htmlspecialchars($jeuImages[$tournoi['jeu']] ?? '../img/lol_cover.webp') ?>" alt="Image du tournoi" loading="lazy">
                        </div>

                        <!-- Description -->
                        <div class="tournoi-description card">
                            <div class="card-body">
                                <div class="card-badge badge-<?= htmlspecialchars($tournoi['statut']) ?>"><?= htmlspecialchars($statutLabels[$tournoi['statut']] ?? $tournoi['statut']) ?></div>
                                <h2 id="titre-tournoi"><?= htmlspecialchars($tournoi['nom']) ?></h2>

                                <h3>📖 Description</h3>
                                <p class="section-description"><?= nl2br(htmlspecialchars((string) ($tournoi['description'] ?: 'Pas de description pour ce tournoi.'))) ?></p>

                                <h4>📜 Règles</h4>
                                <ul>
                                    <li>Uniquement les étudiants du campus sont autorisés à participer.</li>
                                    <li>Comportement fair-play obligatoire.</li>
                                    <li>Les résultats officiels sont définitifs.</li>
                                </ul>

                                <h4>🏆 Récompenses</h4>
                                <dl class="prizes-list">
                                    <dt>💰 Cashprize total</dt><dd><?= number_format((float) $tournoi['cashprize'], 0, ',', ' ') ?>€</dd>
                                    <dt>🥇 1re place (55%)</dt><dd><?= number_format(((float) $tournoi['cashprize']) * 0.55, 0, ',', ' ') ?>€</dd>
                                    <dt>🥈 2e place (30%)</dt><dd><?= number_format(((float) $tournoi['cashprize']) * 0.30, 0, ',', ' ') ?>€</dd>
                                    <dt>🥉 3e place (15%)</dt><dd><?= number_format(((float) $tournoi['cashprize']) * 0.15, 0, ',', ' ') ?>€</dd>
                                    <dt>🎟️ Places max</dt><dd><?= (int) $tournoi['nb_places'] ?> équipes</dd>
                                    <dt>📍 Lieu</dt><dd><?= htmlspecialchars((string) ($tournoi['lieu'] ?: 'Campus')) ?></dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Équipes inscrites -->
                        <div class="card">
                            <div class="card-body">
                                <h3>👥 Équipes inscrites</h3>
                                <?php if (!empty($equipes)): ?>
                                <div class="cards-grid cards-grid-sm" id="equipes-inscrites">
                                    <?php foreach ($equipes as $equipe): ?>
                                    <article class="card card-equipe">
                                        <div class="card-body">
                                            <img src="../img/lol_cover.webp" alt="Équipe" class="equipe-avatar" loading="lazy">
                                            <h4 class="card-title"><?= htmlspecialchars($equipe['nom_equipe']) ?></h4>
                                            <p class="equipe-capitaine">Capitaine: <?= htmlspecialchars($equipe['pseudo']) ?></p>
                                            <span class="status-badge status-<?= htmlspecialchars($equipe['statut']) ?>"><?= htmlspecialchars($equipe['statut']) ?></span>
                                        </div>
                                    </article>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="empty-state">
                                    <span class="empty-state-icon">👥</span>
                                    <p>Aucune équipe inscrite pour le moment.</p>
                                    <?php if ($currentUser): ?>
                                    <p class="empty-state-sub"><a href="espace-membre.php">Voir mon espace membre</a> pour compléter ton profil avant d'inscrire ton équipe.</p>
                                    <?php else: ?>
                                    <p class="empty-state-sub"><a href="inscription.php">Créer un compte</a> pour inscrire ton équipe !</p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Arbre du tournoi -->
                        <div class="card">
                            <div class="card-body">
                                <h3>🌿 Bracket du tournoi</h3>
                                <?php if (count($equipesConfirmees) >= 2): ?>
                                <div class="bracket-grid">
                                    <?php
                                        $bracket = [];
                                        for ($i = 0; $i < count($equipesConfirmees); $i += 2) {
                                            $bracket[] = [
                                                $equipesConfirmees[$i]['nom_equipe'] ?? 'TBD',
                                                $equipesConfirmees[$i + 1]['nom_equipe'] ?? 'TBD',
                                            ];
                                        }
                                    ?>
                                    <?php foreach ($bracket as $index => $match): ?>
                                    <div class="bracket-match">
                                        <p class="bracket-match-title">Match <?= $index + 1 ?></p>
                                        <div class="bracket-team"><?= htmlspecialchars($match[0]) ?></div>
                                        <div class="bracket-vs">VS</div>
                                        <div class="bracket-team"><?= htmlspecialchars($match[1]) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <p class="bracket-help">Le bracket est généré automatiquement à partir des équipes confirmées par l'admin.</p>
                                <?php else: ?>
                                <div class="bracket-placeholder">
                                    <span>🎮</span>
                                    <p>Le bracket sera généré dès qu'il y aura au moins 2 équipes confirmées.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="tournoi-detail-sidebar">
                        <div class="card card-info">
                            <div class="card-body">
                                <h3>📋 Informations</h3>
                                <dl class="info-details">
                                    <dt>🎮 Jeu</dt><dd><?= htmlspecialchars($jeuLabels[$tournoi['jeu']] ?? $tournoi['jeu']) ?></dd>
                                    <dt>📅 Date</dt><dd><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $tournoi['date_debut']))) ?></dd>
                                    <dt>📍 Lieu</dt><dd><?= htmlspecialchars((string) ($tournoi['lieu'] ?: 'Campus')) ?></dd>
                                    <dt>👥 Format</dt><dd>Par équipes</dd>
                                    <dt>📊 Statut</dt><dd><span class="status-badge status-<?= htmlspecialchars($tournoi['statut']) ?>"><?= htmlspecialchars($statutLabels[$tournoi['statut']] ?? $tournoi['statut']) ?></span></dd>
                                    <dt>🏆 Cashprize</dt><dd><?= number_format((float) $tournoi['cashprize'], 0, ',', ' ') ?>€</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="card card-places">
                            <div class="card-body">
                                <h3>🎟️ Places</h3>
                                <?php
                                    $total = max(1, (int) $tournoi['nb_places']);
                                    $inscrits = (int) $tournoi['equipes_inscrites'];
                                    $restantes = max(0, $total - $inscrits);
                                    $ratio = min(100, (int) round(($inscrits / $total) * 100));
                                ?>
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-fill" style="width: <?= $ratio ?>%"></div>
                                </div>
                                <p class="places-restantes"><?= $restantes ?> place(s) restante(s) (<?= $inscrits ?>/<?= $total ?>)</p>
                                <?php if (!$currentUser): ?>
                                <a href="connexion.php?redirect=<?= urlencode('tournoi-detail.php?id=' . (int) $tournoi['id']) ?>" class="btn btn-primary btn-block">Se connecter pour s'inscrire</a>
                                <?php elseif (!in_array($currentUser['role'], ['capitaine', 'admin'], true)): ?>
                                <form method="post" action="tournoi-detail.php?id=<?= (int) $tournoi['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                    <input type="hidden" name="action" value="become_capitaine">
                                    <button type="submit" class="btn btn-primary btn-block">Devenir capitaine</button>
                                </form>
                                <?php elseif ($reservationExistante): ?>
                                <p class="places-restantes">Ton équipe <strong><?= htmlspecialchars($reservationExistante['nom_equipe']) ?></strong> est déjà inscrite.</p>
                                <button type="button" class="btn btn-outline btn-block" disabled>Déjà inscrit</button>
                                <?php elseif ($restantes <= 0): ?>
                                <button type="button" class="btn btn-outline btn-block" disabled>Tournoi complet</button>
                                <?php else: ?>
                                <form method="post" action="tournoi-detail.php?id=<?= (int) $tournoi['id'] ?>" class="reserve-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                    <input type="hidden" name="action" value="reserve">
                                    <label for="nom-equipe" class="sr-only">Nom de l'équipe</label>
                                    <input type="text" id="nom-equipe" name="nom_equipe" required minlength="2" maxlength="50" placeholder="Nom de ton équipe">
                                    <button type="submit" class="btn btn-primary btn-block">Inscrire mon équipe</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
