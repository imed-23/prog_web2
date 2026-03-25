<?php
$rootPath        = '../';
$pageTitle       = 'Blog - Gaming Campus';
$metaDescription = 'Actualités, annonces de tournois et articles de la communauté gaming du campus.';
$cssSpecifique   = '';
$jsSupplementaires = ['blog.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête blog">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Blog</li>
                    </ol>
                </nav>
                <h1>📝 Blog</h1>
                <p class="page-description">Actualités, annonces de tournois et articles de la communauté gaming du campus.</p>
            </div>
        </section>

        <!-- ======== ARTICLE MIS EN AVANT ======== -->
        <section id="article-vedette" aria-labelledby="titre-vedette">
            <div class="section-container">
                <h2 id="titre-vedette" class="sr-only">Article à la une</h2>

                <article class="card card-article card-featured">
                    <img src="../img/lol_cover.webp" alt="Rift Rivals annonce" class="card-image card-image-lg" loading="lazy">
                    <div class="card-body">
                        <div class="article-meta">
                            <span class="article-category">📢 Annonce</span>
                            <time datetime="2026-02-10" class="article-date">10 Février 2026</time>
                        </div>
                        <h3 class="card-title card-title-lg">
                            <a href="article.php">Le Rift Rivals est de retour ! Inscriptions ouvertes</a>
                        </h3>
                        <p class="article-excerpt">Le plus grand tournoi League of Legends du campus revient pour une nouvelle édition. 16 places, 500€ de cashprize et une ambiance de folie. Les inscriptions sont ouvertes dès maintenant !</p>
                        <div class="article-author">
                            <div class="author-avatar-empty" aria-hidden="true">🎮</div>
                            <div>
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">5 min de lecture</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- ======== GRILLE D'ARTICLES ======== -->
        <section id="articles" aria-labelledby="titre-articles">
            <div class="section-container">
                <h2 id="titre-articles">📰 Derniers articles</h2>

                <div class="blog-grid">
                    <article class="card card-article">
                        <img src="../img/fc25.webp" alt="Valorant Campus Showdown" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">🎮 Tournoi</span>
                                <time datetime="2026-02-08" class="article-date">8 Fév 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Valorant Campus Showdown : tout ce qu'il faut savoir</a></h3>
                            <p class="article-excerpt">Le prochain tournoi Valorant approche. Format, règles, prizes et conseils pour vos équipes.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">3 min</span>
                            </div>
                        </div>
                    </article>

                    <article class="card card-article">
                        <img src="../img/cs2.webp" alt="Résultats CS2 Éclair" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">📊 Résultats</span>
                                <time datetime="2026-02-10" class="article-date">10 Fév 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Résultats du dernier tournoi CS2</a></h3>
                            <p class="article-excerpt">Retour sur le dernier tournoi CS2. Les résultats complets et les stats des équipes participantes.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">4 min</span>
                            </div>
                        </div>
                    </article>

                    <article class="card card-article">
                        <img src="../img/mario_kart.webp" alt="Guide inscription" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">📖 Guide</span>
                                <time datetime="2026-02-05" class="article-date">5 Fév 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Comment s'inscrire à un tournoi ? Le guide complet</a></h3>
                            <p class="article-excerpt">Nouveau sur la plateforme ? Voici un guide étape par étape pour créer ton équipe et t'inscrire à ton premier tournoi.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">6 min</span>
                            </div>
                        </div>
                    </article>

                    <article class="card card-article">
                        <img src="../img/ssbu.webp" alt="LAN Party annonce" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">📢 Annonce</span>
                                <time datetime="2026-02-01" class="article-date">1 Fév 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Grande LAN Party le 28 février : 100 places !</a></h3>
                            <p class="article-excerpt">Le BDE organise la plus grande LAN Party de l'année ! Ramène ton PC, on s'occupe du reste.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">2 min</span>
                            </div>
                        </div>
                    </article>

                    <article class="card card-article">
                        <img src="../img/rocket_league.webp" alt="Interview joueur" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">🎙️ Interview</span>
                                <time datetime="2026-01-28" class="article-date">28 Jan 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Interview : Le joueur n°1 du campus parle de son parcours</a></h3>
                            <p class="article-excerpt">On a rencontré le leader du classement. Il nous parle de son parcours gaming et de ses ambitions.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">8 min</span>
                            </div>
                        </div>
                    </article>

                    <article class="card card-article">
                        <img src="../img/fc25.webp" alt="Saison 2 annonce" class="card-image" loading="lazy">
                        <div class="card-body">
                            <div class="article-meta">
                                <span class="article-category">📢 Annonce</span>
                                <time datetime="2026-01-20" class="article-date">20 Jan 2026</time>
                            </div>
                            <h3 class="card-title"><a href="article.php">Saison 2 des tournois : le calendrier complet</a></h3>
                            <p class="article-excerpt">La saison 2 des tournois Gaming Campus commence en février. Découvrez le calendrier complet et les nouveautés.</p>
                            <div class="article-author">
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">4 min</span>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <nav class="pagination" aria-label="Pagination des articles">
                    <ul>
                        <li><a href="#" class="pagination-link active" aria-current="page">1</a></li>
                        <li><a href="#" class="pagination-link">2</a></li>
                        <li><a href="#" class="pagination-link pagination-next" aria-label="Page suivante">→</a></li>
                    </ul>
                </nav>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
