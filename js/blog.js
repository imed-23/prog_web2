/* ============================================================
   GAMING CAMPUS — blog.js
   Pagination côté client des articles de blog
   Chargé uniquement dans pages/blog.php
   ============================================================ */

(function () {
    var PER_PAGE  = 4;
    var grid      = document.querySelector('.blog-grid');
    if (!grid) return;

    var articles   = Array.from(grid.querySelectorAll('.card-article'));
    var pageLinks  = Array.from(document.querySelectorAll('.pagination .pagination-link:not(.pagination-next)'));
    var nextBtn    = document.querySelector('.pagination .pagination-next');
    var totalPages = Math.ceil(articles.length / PER_PAGE);

    function showPage(page) {
        var start = (page - 1) * PER_PAGE;
        articles.forEach(function (art, i) {
            art.style.display = (i >= start && i < start + PER_PAGE) ? '' : 'none';
        });
        pageLinks.forEach(function (link) {
            var isActive = parseInt(link.textContent.trim()) === page;
            link.classList.toggle('active', isActive);
            if (isActive) link.setAttribute('aria-current', 'page');
            else          link.removeAttribute('aria-current');
        });
        if (nextBtn) {
            nextBtn.style.opacity       = page >= totalPages ? '0.4' : '';
            nextBtn.style.pointerEvents = page >= totalPages ? 'none' : '';
        }
    }

    pageLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var page = parseInt(link.textContent.trim());
            if (!isNaN(page)) showPage(page);
        });
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var active = pageLinks.find(function (l) { return l.classList.contains('active'); });
            if (!active) return;
            var cur = parseInt(active.textContent.trim());
            if (cur < totalPages) showPage(cur + 1);
        });
    }

    showPage(1);
}());
