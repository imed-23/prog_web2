<?php
$rootPath        = '../';
$pageTitle       = 'Article - Gaming Campus';
$metaDescription = 'Actualités et articles de la communauté Gaming Campus.';
$cssSpecifique   = '';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête article">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li aria-current="page">Article</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- ======== CONTENU ARTICLE ======== -->
        <section id="article-content" aria-labelledby="titre-article">
            <div class="section-container">
                <div class="article-layout">

                    <!-- Corps de l'article -->
                    <article class="article-main">
                        <div class="article-meta">
                            <span class="article-category">📢 Annonce</span>
                            <time datetime="2026-02-10" class="article-date">10 Février 2026</time>
                        </div>

                        <h1 id="titre-article">Le Rift Rivals est de retour ! Inscriptions ouvertes</h1>

                        <div class="article-author">
                            <div class="author-avatar-empty" aria-hidden="true">🎮</div>
                            <div>
                                <span class="author-name">BDE Gaming</span>
                                <span class="reading-time">5 min de lecture</span>
                            </div>
                        </div>

                        <figure class="article-figure">
                            <img src="../img/lol_cover.webp" alt="League of Legends Rift Rivals" loading="lazy" class="article-img">
                        </figure>

                        <div class="article-body">
                            <p>Le tournoi Rift Rivals est de retour pour une nouvelle saison ! Cette année encore, 16 équipes s'affronteront dans un format Bo3 (phases de poules) puis Bo5 (phases finales).</p>
                            <h2>Format et règles</h2>
                            <p>Les équipes sont composées de 5 joueurs + 1 remplaçant. Les inscriptions sont ouvertes jusqu'à 72h avant le début du tournoi.</p>
                            <h2>Comment s'inscrire ?</h2>
                            <p>Rendez-vous sur la <a href="tournois.php">page Tournois</a> et inscrivez votre équipe dès maintenant !</p>
                        </div>

                        <footer class="article-footer">
                            <div class="article-tags">
                                <span class="tag">League of Legends</span>
                                <span class="tag">Tournoi</span>
                                <span class="tag">Inscriptions</span>
                            </div>
                            <div class="article-share">
                                Partager :
                                <a href="#" aria-label="Partager sur Twitter">🐦 Twitter</a>
                                <a href="#" aria-label="Partager sur Discord">🎧 Discord</a>
                            </div>
                        </footer>
                    </article>

                    <!-- Sidebar -->
                    <aside class="article-sidebar">
                        <div class="sidebar-card">
                            <h3>📰 Articles récents</h3>
                            <ul class="recent-articles">
                                <li><a href="article.php">Valorant Campus Showdown</a></li>
                                <li><a href="article.php">Résultats CS2 Éclair</a></li>
                                <li><a href="article.php">Guide inscription tournois</a></li>
                            </ul>
                        </div>
                        <div class="sidebar-card">
                            <h3>🏷️ Catégories</h3>
                            <ul class="categories-list">
                                <li><a href="blog.php">📢 Annonces</a></li>
                                <li><a href="blog.php">🎮 Tournois</a></li>
                                <li><a href="blog.php">📊 Résultats</a></li>
                                <li><a href="blog.php">📖 Guides</a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
