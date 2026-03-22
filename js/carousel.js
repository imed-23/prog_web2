/* ============================================================
   GAMING CAMPUS — carousel.js
   Carousel tactile pour la section "Tournois en cours"
   Sera activé en Sprint 4 une fois les données réelles chargées
   depuis la base de données via PHP/MySQL.

   Fonctionnalités :
   - Navigation précédent / suivant
   - Points de navigation (dots)
   - Support tactile (swipe mobile)
   - Responsive (recalcul au redimensionnement)
   ============================================================ */
(function () {
    var section = document.getElementById('tournois-en-cours');
    if (!section) return;
    var grid = section.querySelector('.cards-grid');
    if (!grid) return;

    var cards = Array.from(grid.children);
    if (cards.length === 0) return;
    var total   = cards.length;
    var current = 0;

    /* Envelopper la grille dans un viewport de carousel */
    grid.classList.add('carousel-track');
    var viewport = document.createElement('div');
    viewport.className = 'carousel-viewport';
    grid.parentNode.insertBefore(viewport, grid);
    viewport.appendChild(grid);

    /* Envelopper viewport + boutons dans un wrapper */
    var wrapper = document.createElement('div');
    wrapper.className = 'carousel-wrapper';
    wrapper.setAttribute('aria-label', 'Carrousel des tournois en cours');
    viewport.parentNode.insertBefore(wrapper, viewport);
    wrapper.appendChild(viewport);

    var prevBtn = document.createElement('button');
    prevBtn.className = 'carousel-btn carousel-prev';
    prevBtn.setAttribute('aria-label', 'Tournoi précédent');
    prevBtn.innerHTML = '&#8592;';
    wrapper.insertBefore(prevBtn, viewport);

    var nextBtn = document.createElement('button');
    nextBtn.className = 'carousel-btn carousel-next';
    nextBtn.setAttribute('aria-label', 'Tournoi suivant');
    nextBtn.innerHTML = '&#8594;';
    wrapper.appendChild(nextBtn);

    /* Dots de navigation */
    var dotsEl = document.createElement('div');
    dotsEl.className = 'carousel-dots';
    dotsEl.setAttribute('role', 'tablist');
    wrapper.parentNode.insertBefore(dotsEl, wrapper.nextSibling);

    cards.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.className  = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('role', 'tab');
        dot.setAttribute('aria-label', 'Tournoi ' + (i + 1));
        dot.addEventListener('click', function () { goTo(i); });
        dotsEl.appendChild(dot);
    });

    function update(animate) {
        grid.style.transition = animate === false
            ? 'none'
            : 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        grid.style.transform = 'translateX(-' + (current * viewport.offsetWidth) + 'px)';
        dotsEl.querySelectorAll('.carousel-dot').forEach(function (d, i) {
            d.classList.toggle('active', i === current);
        });
    }

    function goTo(idx) {
        current = ((idx % total) + total) % total;
        update(true);
    }

    prevBtn.addEventListener('click', function () { goTo(current - 1); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); });

    /* Support tactile (swipe) */
    var startX = 0;
    viewport.addEventListener('touchstart', function (e) {
        startX = e.touches[0].clientX;
    }, { passive: true });
    viewport.addEventListener('touchend', function (e) {
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 50) goTo(current + (dx < 0 ? 1 : -1));
    }, { passive: true });

    window.addEventListener('resize', function () { update(false); });
    update(false);
}());
