<?php
require_once __DIR__ . '/assets/php/config/auth.php';
require_once __DIR__ . '/assets/php/config/db.php';
gc_start_session();
$currentUser = gc_current_user();

$rootPath        = '';
$pageTitle       = 'Gaming Campus - Plateforme de Tournois';
$metaDescription = 'Plateforme de Tournois Gaming Campus - Consultez les tournois, inscrivez votre équipe et suivez les classements.';
$cssSpecifique   = 'index.css';
include 'assets/php/components/header.php';

// --- Tournois en cours ---
$tournoIsEnCours = [];
try {
    $stmt = $pdo->query("SELECT t.id, t.nom, t.jeu, t.image, t.date_debut, t.lieu, t.nb_places, t.cashprize, t.statut,
                         COALESCE(rc.inscrits, 0) AS equipes_inscrites
                         FROM tournois t
                         LEFT JOIN (SELECT tournoi_id, COUNT(*) AS inscrits FROM reservations WHERE statut != 'annulee' GROUP BY tournoi_id) rc ON rc.tournoi_id = t.id
                         WHERE t.statut = 'en-cours'
                         ORDER BY t.date_debut ASC LIMIT 3");
    $tournoIsEnCours = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[INDEX EN-COURS] ' . $e->getMessage());
}

// --- Prochains tournois (à venir) ---
$prochainsMatchs = [];
try {
    $stmt = $pdo->query("SELECT t.id, t.nom, t.jeu, t.image, t.date_debut, t.lieu, t.nb_places, t.cashprize,
                         COALESCE(rc.inscrits, 0) AS equipes_inscrites
                         FROM tournois t
                         LEFT JOIN (SELECT tournoi_id, COUNT(*) AS inscrits FROM reservations WHERE statut != 'annulee' GROUP BY tournoi_id) rc ON rc.tournoi_id = t.id
                         WHERE t.statut = 'a-venir'
                         ORDER BY t.date_debut ASC LIMIT 3");
    $prochainsMatchs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[INDEX A-VENIR] ' . $e->getMessage());
}

// --- Classement Top 5 ---
$topClassement = [];
try {
    $stmt = $pdo->query("SELECT u.id, u.pseudo, u.avatar, u.jeu_principal,
                         (SELECT r2.nom_equipe FROM reservations r2 WHERE r2.capitaine_id = u.id ORDER BY r2.created_at DESC LIMIT 1) AS nom_equipe,
                         SUM(CASE WHEN r.statut = 'confirmee' THEN 1 ELSE 0 END) AS victoires,
                         SUM(CASE WHEN r.statut = 'confirmee' THEN 10 WHEN r.statut = 'en-attente' THEN 3 ELSE 0 END) AS points
                         FROM utilisateurs u
                         LEFT JOIN reservations r ON r.capitaine_id = u.id
                         WHERE u.role <> 'admin'
                         GROUP BY u.id, u.pseudo, u.avatar, u.jeu_principal
                         ORDER BY points DESC, victoires DESC, u.created_at ASC
                         LIMIT 5");
    $topClassement = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[INDEX CLASSEMENT] ' . $e->getMessage());
}

$jeuLabels = [
    'lol'          => 'League of Legends',
    'valorant'     => 'Valorant',
    'cs2'          => 'CS2',
    'fortnite'     => 'Fortnite',
    'rocket-league'=> 'Rocket League',
    'autre'        => 'Autre',
];
?>

    <!-- contenu principal -->
    <main id="main-content">

        <!-- section hero -->
        <section id="hero" class="hero-section" aria-label="Bannière principale">
            <div class="hero-content">
                <h1>Rejoins la compétition <span class="text-accent">Gaming Campus</span></h1>
                <p class="hero-subtitle">Consulte les tournois à venir, inscris ton équipe et grimpe dans le classement. La prochaine victoire est à portée de clic.</p>
                <div class="hero-actions">
                    <a href="pages/tournois.php" class="btn btn-primary btn-lg">Voir les Tournois</a>
                    <?php if ($currentUser): ?>
                    <a href="pages/espace-membre.php" class="btn btn-outline btn-lg">Voir mon compte</a>
                    <?php else: ?>
                    <a href="pages/inscription.php" class="btn btn-outline btn-lg">Créer un Compte</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- section tournois en cours -->
        <section id="tournois-en-cours" aria-labelledby="titre-tournois-en-cours">
            <div class="section-container">
                <div class="section-header">
                    <h2 id="titre-tournois-en-cours">🎮 Tournois en Cours</h2>
                    <a href="pages/tournois.php" class="section-link">Voir tous les tournois →</a>
                </div>

                <?php if (!empty($tournoIsEnCours)): ?>
                <div class="tournois-grid">
                    <?php foreach ($tournoIsEnCours as $t): ?>
                    <article class="tournoi-card">
                        <?php if (!empty($t['image'])): ?>
                            <img src="<?= htmlspecialchars($t['image']) ?>" alt="Image de <?= htmlspecialchars($t['nom']) ?>" class="tournoi-card-img">
                        <?php else: ?>
                            <div class="tournoi-card-img-placeholder">🎮</div>
                        <?php endif; ?>
                        
                        <div class="tournoi-card-content">
                            <div class="tournoi-card-header">
                                <span class="tournoi-jeu"><?= htmlspecialchars($jeuLabels[$t['jeu']] ?? ucfirst($t['jeu'])) ?></span>
                                <span class="badge badge-en-cours">En cours</span>
                            </div>
                            <h3 class="tournoi-card-title"><?= htmlspecialchars($t['nom']) ?></h3>
                            <div class="tournoi-card-meta">
                                <span>📅 <?= htmlspecialchars(date('d/m/Y', strtotime((string) $t['date_debut']))) ?></span>
                                <span>📍 <?= htmlspecialchars($t['lieu'] ?? 'Campus') ?></span>
                                <span>👥 <?= (int) $t['equipes_inscrites'] ?> / <?= (int) $t['nb_places'] ?> équipes</span>
                                <?php if ($t['cashprize'] > 0): ?>
                                <span>💰 <?= number_format((float) $t['cashprize'], 0, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                            <a href="pages/tournoi-detail.php?id=<?= (int) $t['id'] ?>" class="btn btn-primary btn-sm">Voir le tournoi</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="empty-state-icon">🎮</span>
                    <p>Aucun tournoi en cours pour le moment.</p>
                    <p style="font-size: 0.9em; color: var(--text-muted); max-width: 600px; margin: 0 auto 1rem auto;">
                        Un tournoi passe en statut "En cours" à sa date de début, si un nombre suffisant d'équipes (généralement au moins 2) est inscrit. Pensez à vous inscrire aux tournois "À venir" !
                    </p>
                    <p class="empty-state-sub"><a href="pages/tournois.php">Voir tous les tournois</a></p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- section prochains matchs -->
        <section id="prochains-matchs" aria-labelledby="titre-prochains-matchs">
            <div class="section-container">
                <div class="section-header">
                    <h2 id="titre-prochains-matchs">⏱️ Prochains Tournois</h2>
                    <a href="pages/tournois.php?statut=a-venir" class="section-link">Voir tous →</a>
                </div>

                <?php if (!empty($prochainsMatchs)): ?>
                <div class="matchs-list" id="liste-matchs">
                    <?php foreach ($prochainsMatchs as $t): ?>
                    <div class="match-item">
                        <div class="match-info">
                            <span class="match-jeu"><?= htmlspecialchars($jeuLabels[$t['jeu']] ?? ucfirst($t['jeu'])) ?></span>
                            <strong class="match-nom"><?= htmlspecialchars($t['nom']) ?></strong>
                            <span class="match-date">📅 <?= htmlspecialchars(date('d/m/Y à H\hi', strtotime((string) $t['date_debut']))) ?></span>
                            <span class="match-lieu">📍 <?= htmlspecialchars($t['lieu'] ?? 'Campus') ?></span>
                        </div>
                        <div class="match-places">
                            <span><?= (int) $t['equipes_inscrites'] ?> / <?= (int) $t['nb_places'] ?> équipes</span>
                            <a href="pages/tournoi-detail.php?id=<?= (int) $t['id'] ?>" class="btn btn-outline btn-sm">S'inscrire</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="matchs-list" id="liste-matchs">
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">⏱️</span>
                        <p>Aucun tournoi à venir pour le moment.</p>
                        <p class="empty-state-sub"><a href="pages/tournois.php">Voir tous les tournois</a></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- section apercu classement -->
        <section id="classement-apercu" aria-labelledby="titre-classement">
            <div class="section-container">
                <div class="section-header">
                    <h2 id="titre-classement">🏆 Classement - Top 5</h2>
                    <a href="pages/classement.php" class="section-link">Voir le classement complet →</a>
                </div>

                <table class="leaderboard-table" aria-label="Top 5 du classement général">
                    <thead>
                        <tr>
                            <th scope="col">Rang</th>
                            <th scope="col">Joueur</th>
                            <th scope="col">Équipe</th>
                            <th scope="col">Victoires</th>
                            <th scope="col">Points</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboard-preview-body">
                        <?php if (!empty($topClassement)): ?>
                        <?php foreach ($topClassement as $rang => $row): ?>
                        <tr>
                            <td>
                                <?php if ($rang === 0): ?>🥇
                                <?php elseif ($rang === 1): ?>🥈
                                <?php elseif ($rang === 2): ?>🥉
                                <?php else: ?>#<?= $rang + 1 ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="pages/profil.php?id=<?= (int) $row['id'] ?>">
                                    <?= htmlspecialchars($row['pseudo']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['nom_equipe'] ?? '—') ?></td>
                            <td><?= (int) $row['victoires'] ?></td>
                            <td><strong><?= (int) $row['points'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr class="empty-state-row">
                            <td colspan="5" class="empty-state-cell">
                                Aucun joueur classé pour le moment.
                                <?php if ($currentUser): ?>
                                <a href="pages/espace-membre.php">Voir mon compte</a> pour suivre tes stats.
                                <?php else: ?>
                                <a href="pages/inscription.php">Inscris-toi</a> pour apparaître ici !
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- section inscription -->
        <section id="cta-inscription" class="cta-section" aria-labelledby="titre-cta">
            <div class="section-container">
                <h2 id="titre-cta">Prêt à entrer dans l'arène ?</h2>
                <p>Crée ton compte, forme ton équipe et inscris-toi au prochain tournoi. Les places partent vite !</p>
                <div class="cta-actions">
                    <?php if ($currentUser): ?>
                    <a href="pages/espace-membre.php" class="btn btn-primary btn-lg">Voir mon espace membre</a>
                    <?php else: ?>
                    <a href="pages/inscription.php" class="btn btn-primary btn-lg">Créer mon compte</a>
                    <?php endif; ?>
                    <a href="pages/tournois.php" class="btn btn-outline btn-lg">Explorer les tournois</a>
                </div>
            </div>
        </section>

    </main>

<?php
$jsSupplementaires = [];
include 'assets/php/components/footer.php';
?>
