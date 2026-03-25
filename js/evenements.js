/* ============================================================
   GAMING CAMPUS — evenements.js
   Calendrier interactif — navigation entre les mois
   Chargé uniquement dans pages/evenements.php
   ============================================================ */

(function () {
    var MONTHS = ['Janvier','Février','Mars','Avril','Mai','Juin',
                  'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    var DAYS   = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

    /* Événements : clé = "AAAA-MM-JJ" → tableau d'objets {label, href} */
    var EVENTS = window.GC_EVENTS || {};

    var year      = new Date().getFullYear();
    var month     = new Date().getMonth(); /* 0 = Janvier … 11 = Décembre */
    var container = document.getElementById('calendar-container');
    var titleH2   = document.getElementById('titre-calendrier');
    var labelSpan = document.getElementById('cal-label');
    var prevBtn   = document.getElementById('cal-prev');
    var nextBtn   = document.getElementById('cal-next');

    if (!container) return;

    function pad(n) { return String(n).padStart(2, '0'); }

    function render() {
        var mName = MONTHS[month];
        var label = mName + ' ' + year;

        /* Mise à jour des titres */
        if (titleH2)   titleH2.textContent = '📅 Calendrier - ' + label;
        if (labelSpan) labelSpan.textContent = label;

        var prevMonthLabel = month > 0  ? MONTHS[month - 1] : MONTHS[11] + ' ' + (year - 1);
        var nextMonthLabel = month < 11 ? MONTHS[month + 1] : MONTHS[0]  + ' ' + (year + 1);
        if (prevBtn) prevBtn.textContent = '← ' + prevMonthLabel;
        if (nextBtn) nextBtn.textContent = nextMonthLabel + ' →';

        /* Premier jour de la semaine (Lun=0 … Dim=6) */
        var firstDay    = new Date(year, month, 1).getDay(); /* 0=Dim */
        var startCol    = firstDay === 0 ? 6 : firstDay - 1;
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var today       = new Date();

        var html = '<div class="table-responsive">'
            + '<table class="calendar-table" aria-label="Calendrier ' + label + '">'
            + '<thead><tr>';
        DAYS.forEach(function (d) { html += '<th scope="col">' + d + '</th>'; });
        html += '</tr></thead><tbody>';

        var day     = 1;
        var started = false;
        while (day <= daysInMonth) {
            html += '<tr>';
            for (var col = 0; col < 7; col++) {
                if (!started && col < startCol) {
                    html += '<td></td>';
                } else if (day > daysInMonth) {
                    html += '<td></td>';
                } else {
                    started = true;
                    var key  = year + '-' + pad(month + 1) + '-' + pad(day);
                    var evts = EVENTS[key] || [];
                    var isToday = (today.getFullYear() === year
                                && today.getMonth()    === month
                                && today.getDate()     === day);
                    var cls = '';
                    if (isToday)     cls += ' calendar-today';
                    if (evts.length) cls += ' calendar-event';
                    html += '<td class="' + cls.trim() + '">';
                    html += '<span>' + day + '</span>';
                    evts.forEach(function (e) {
                        html += ' <a href="' + e.href + '" class="calendar-event-link">' + e.label + '</a>';
                    });
                    html += '</td>';
                    day++;
                }
            }
            html += '</tr>';
        }
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (month === 0) { month = 11; year--; } else { month--; }
            render();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (month === 11) { month = 0; year++; } else { month++; }
            render();
        });
    }

    render();
}());
