/* ============================================================
   GAMING CAMPUS — main.js
   Scripts globaux : menu hamburger + lien actif automatique
   Chargé dans TOUTES les pages (dans le <head> ou avant </body>)
   ============================================================ */

/* ── 1. Menu hamburger (mobile) ── */
(function () {
    var THEME_KEY = 'gc_theme';
    var root = document.documentElement;
    var toggles = document.querySelectorAll('[data-theme-toggle]');

    function preferredTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            return 'light';
        }
        return 'dark';
    }

    function updateToggleUi(theme) {
        toggles.forEach(function (btn) {
            var icon = btn.querySelector('.theme-toggle-icon');
            var label = btn.querySelector('.theme-toggle-label');
            if (icon) icon.textContent = (theme === 'light') ? '☀️' : '🌙';
            if (label) label.textContent = (theme === 'light') ? 'Light' : 'Dark';
            btn.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
            btn.setAttribute('title', 'Thème : ' + ((theme === 'light') ? 'Light' : 'Dark'));
        });
    }

    function applyTheme(theme) {
        if (theme === 'light') {
            root.setAttribute('data-theme', 'light');
        } else {
            root.removeAttribute('data-theme');
            theme = 'dark';
        }
        updateToggleUi(theme);
    }

    var storedTheme = localStorage.getItem(THEME_KEY);
    var currentTheme = (storedTheme === 'light' || storedTheme === 'dark') ? storedTheme : preferredTheme();
    applyTheme(currentTheme);

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentTheme = (root.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
            localStorage.setItem(THEME_KEY, currentTheme);
            applyTheme(currentTheme);
        });
    });
}());


/* ── 1. Menu hamburger (mobile) ── */
(function () {
    var toggle = document.querySelector('.menu-toggle');
    var nav    = document.getElementById('main-nav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
        var isOpen = nav.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    });

    /* Fermer le menu au clic sur un lien (UX mobile) */
    nav.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            nav.classList.remove('nav-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Ouvrir le menu');
        });
    });
}());


/* ── 2. Lien actif automatique dans la navigation ── */
/* Détecte l'URL courante et ajoute "active" + aria-current sur le bon lien */
(function () {
    var currentPath = window.location.pathname;

    /* Normalise un href en chemin absolu pour comparaison */
    function resolvePath(href) {
        var a = document.createElement('a');
        a.href = href;
        return a.pathname;
    }

    document.querySelectorAll('.nav-link').forEach(function (link) {
        /* Retirer toute classe active déjà mise manuellement */
        link.classList.remove('active');
        link.removeAttribute('aria-current');

        var linkPath = resolvePath(link.getAttribute('href'));

        /* Correspondance exacte, ou page d'accueil (index.html/index.php) */
        var isHome = (linkPath === '/index.html' || linkPath === '/index.php' || linkPath === '/');
        var atHome = (currentPath === '/index.html' || currentPath === '/index.php' || currentPath === '/');

        if (linkPath === currentPath || (isHome && atHome)) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
}());
