/* ============================================================
   GAMING CAMPUS — classement.js
   Tri du tableau de classement + filtres par jeu
   Chargé uniquement dans pages/classement.php
   ============================================================ */

/* ── 1. Filter tab buttons ── */
(function () {
    var tabs = document.querySelectorAll('.filter-tabs .tab-btn');
    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            tabs.forEach(function (t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            /* Filter visible rows by game (data-game attribute on rows) */
            var selectedGame = btn.textContent.trim();
            var rows = document.querySelectorAll('#leaderboard-body tr:not(.empty-state-row)');
            rows.forEach(function (row) {
                var game = row.cells[3] ? row.cells[3].textContent.trim() : '';
                if (selectedGame === 'Tous les jeux' || game === selectedGame) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
}());

/* ── 2. Leaderboard table sort ── */
(function () {
    var table = document.getElementById('leaderboard-table');
    if (!table) return;

    var tbody        = table.querySelector('tbody');
    var headers      = table.querySelectorAll('th.sortable');
    var currentCol   = -1;
    var currentOrder = 'asc';

    /* Parse a cell value based on declared data-type */
    function parseValue(cell, type) {
        var raw = cell.textContent.trim();
        if (type === 'number')  return parseFloat(raw.replace(/[^\d.-]/g, '')) || 0;
        if (type === 'percent') return parseFloat(raw.replace('%', '')) || 0;
        return raw.toLowerCase();
    }

    function sortTable(colIndex, type) {
        var rows = Array.from(tbody.querySelectorAll('tr:not(.empty-state-row)'));
        if (rows.length === 0) return;

        /* Toggle order if same column clicked */
        if (colIndex === currentCol) {
            currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentCol   = colIndex;
            currentOrder = 'asc';
        }

        rows.sort(function (a, b) {
            var cellA = a.cells[colIndex];
            var cellB = b.cells[colIndex];
            if (!cellA || !cellB) return 0;

            var va = parseValue(cellA, type);
            var vb = parseValue(cellB, type);

            var cmp;
            if (typeof va === 'number' && typeof vb === 'number') {
                cmp = va - vb;
            } else {
                cmp = String(va).localeCompare(String(vb), 'fr', { sensitivity: 'base' });
            }
            return currentOrder === 'asc' ? cmp : -cmp;
        });

        /* Re-append sorted rows */
        rows.forEach(function (row) { tbody.appendChild(row); });

        /* Update aria-sort and icons */
        headers.forEach(function (th) {
            th.removeAttribute('aria-sort');
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = '↕';
        });
        var activeHeader = table.querySelector('th.sortable[data-col="' + colIndex + '"]');
        if (activeHeader) {
            activeHeader.setAttribute('aria-sort', currentOrder === 'asc' ? 'ascending' : 'descending');
            var icon = activeHeader.querySelector('.sort-icon');
            if (icon) icon.textContent = currentOrder === 'asc' ? '↑' : '↓';
        }
    }

    headers.forEach(function (th) {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function () {
            var colIndex = parseInt(this.getAttribute('data-col'), 10);
            var type     = this.getAttribute('data-type') || 'text';
            sortTable(colIndex, type);
        });

        /* Keyboard accessibility */
        th.setAttribute('tabindex', '0');
        th.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                th.click();
            }
        });
    });
}());
