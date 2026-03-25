<?php
require_once __DIR__ . '/../assets/php/config/db.php';

$events = [];
$calendarEvents = [];
$dbError = '';

$jeuLabels = [
    'lol' => 'League of Legends',
    'valorant' => 'Valorant',
    'cs2' => 'Counter-Strike 2',
    'fortnite' => 'Fortnite',
    'rocket-league' => 'Rocket League',
];

try {
    $stmt = $pdo->query('SELECT id, nom, jeu, date_debut, lieu, cashprize, statut FROM tournois ORDER BY date_debut ASC LIMIT 24');
    $events = $stmt->fetchAll();

    foreach ($events as $event) {
        $key = date('Y-m-d', strtotime((string) $event['date_debut']));
        if (!isset($calendarEvents[$key])) {
            $calendarEvents[$key] = [];
        }
        $calendarEvents[$key][] = [
            'label' => (string) $event['nom'],
            'href' => 'tournoi-detail.php?id=' . (int) $event['id'],
        ];
    }
} catch (PDOException $e) {
    error_log('[EVENEMENTS LIST] ' . $e->getMessage());
    $dbError = 'Impossible de charger les événements pour le moment.';
}

$rootPath        = '../';
$pageTitle       = 'Événements - Gaming Campus';
$metaDescription = 'Calendrier des événements gaming du campus. Dates, lieux, prix et inscriptions.';
$cssSpecifique   = 'evenements.css';
$jsSupplementaires = ['evenements.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête événements">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Événements</li>
                    </ol>
                </nav>
                <h1>📅 Événements</h1>
                <p class="page-description">Tous les événements gaming du campus. LAN parties, soirées e-sport, conférences et plus encore.</p>
            </div>
        </section>

        <!-- ======== CALENDRIER INTERACTIF ======== -->
        <section id="calendrier" aria-labelledby="titre-calendrier">
            <div class="section-container">
                <h2 id="titre-calendrier">📅 Calendrier</h2>

                <div class="calendar-navigation">
                    <button id="cal-prev" class="btn btn-outline" aria-label="Mois précédent">←</button>
                    <span class="calendar-current-month"><strong id="cal-label">Chargement...</strong></span>
                    <button id="cal-next" class="btn btn-outline" aria-label="Mois suivant">→</button>
                </div>

                <div id="calendar-container"></div>
                <script>
                    window.GC_EVENTS = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                </script>
            </div>
        </section>

        <!-- ======== LISTE DES ÉVÉNEMENTS ======== -->
        <section id="liste-evenements" aria-labelledby="titre-evenements">
            <div class="section-container">
                <h2 id="titre-evenements">🎯 Événements à venir</h2>

                <div class="events-list">
                    <?php if ($dbError !== ''): ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">⚠️</span>
                        <p><?= htmlspecialchars($dbError) ?></p>
                    </div>
                    <?php elseif (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <div class="event-date">
                            <time datetime="<?= htmlspecialchars((string) $event['date_debut']) ?>">
                                <span class="event-day"><?= htmlspecialchars(date('d', strtotime((string) $event['date_debut']))) ?></span>
                                <span class="event-month"><?= htmlspecialchars(date('M', strtotime((string) $event['date_debut']))) ?></span>
                            </time>
                        </div>
                        <div class="event-body">
                            <h3 class="event-title"><?= htmlspecialchars($event['nom']) ?></h3>
                            <ul class="event-details">
                                <li>🎮 <?= htmlspecialchars($jeuLabels[$event['jeu']] ?? $event['jeu']) ?></li>
                                <li>🕒 <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $event['date_debut']))) ?></li>
                                <li>📍 <?= htmlspecialchars((string) ($event['lieu'] ?: 'Campus')) ?></li>
                                <li>🏆 <?= number_format((float) $event['cashprize'], 0, ',', ' ') ?>€</li>
                            </ul>
                        </div>
                        <div class="event-actions">
                            <a href="tournoi-detail.php?id=<?= (int) $event['id'] ?>" class="btn btn-primary">Détails</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">📅</span>
                        <p>Aucun événement à venir pour le moment.</p>
                        <p class="empty-state-sub">Les prochains événements seront annoncés ici dès leur programmation.</p>
                        <a href="contact.php" class="btn btn-outline">Nous contacter</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
