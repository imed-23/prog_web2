/* ============================================================
   GAMING CAMPUS — tournois-filtres.js
   Filtrage par jeu/statut/places + pagination
    Chargé uniquement dans pages/tournois.php
   ============================================================ */
(function () {
    var PER_PAGE   = 3;
    var allCards   = Array.from(document.querySelectorAll('#liste-tournois .card-tournoi'));
    var filterForm = document.querySelector('.filters-form');
    var resultEl   = document.querySelector('.results-summary p');
    var pageLinks  = Array.from(document.querySelectorAll('.pagination .pagination-link:not(.pagination-next)'));
    var nextBtn    = document.querySelector('.pagination .pagination-next');

    if (!filterForm) return;

    var filtered = allCards.slice();
    var curPage  = 1;

    function applyFilters() {
        var jeu    = document.getElementById('filter-jeu').value;
        var statut = document.getElementById('filter-statut').value;
        var places = document.getElementById('filter-places').value;

        filtered = allCards.filter(function (card) {
            if (jeu    && card.dataset.jeu    !== jeu)    return false;
            if (statut && card.dataset.statut !== statut) return false;
            if (places && card.dataset.places !== places) return false;
            return true;
        });
        curPage = 1;
        render();
    }

    function render() {
        var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        var start = (curPage - 1) * PER_PAGE;
        var end   = start + PER_PAGE;

        /* Afficher/masquer les cartes */
        allCards.forEach(function (c) { c.style.display = 'none'; });
        filtered.slice(start, end).forEach(function (c) { c.style.display = ''; });

        /* Compteur de résultats */
        if (resultEl) {
            var n = filtered.length;
            resultEl.innerHTML = '<strong>' + n + ' tournoi' + (n !== 1 ? 's' : '') + '</strong> trouvé' + (n !== 1 ? 's' : '');
        }

        /* Pagination */
        pageLinks.forEach(function (link) {
            var page = parseInt(link.textContent.trim(), 10);
            link.style.display = page <= totalPages ? '' : 'none';
            var active = page === curPage;
            link.classList.toggle('active', active);
            if (active) link.setAttribute('aria-current', 'page');
            else        link.removeAttribute('aria-current');
        });

        if (nextBtn) {
            var atLast = curPage >= totalPages;
            nextBtn.style.opacity       = atLast ? '0.4' : '';
            nextBtn.style.pointerEvents = atLast ? 'none' : '';
        }
    }

    /* Soumission du formulaire (bouton Filtrer) */
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        applyFilters();
    });

    /* Filtrage en temps réel au changement de select */
    filterForm.querySelectorAll('select').forEach(function (sel) {
        sel.addEventListener('change', applyFilters);
    });

    /* Clics de pagination */
    pageLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var page = parseInt(link.textContent.trim(), 10);
            if (!isNaN(page)) {
                curPage = page;
                render();
                document.getElementById('liste-tournois').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
            if (curPage < totalPages) {
                curPage++;
                render();
                document.getElementById('liste-tournois').scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    render();
}());
