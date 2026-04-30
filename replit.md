# Gaming Campus

A web application for viewing and managing gaming tournaments. Users can browse tournaments, create accounts, and register their teams. Administrators have a back-office for managing tournaments, users, and reservations.

## Tech Stack

- **Backend**: PHP 8.2 (built-in server)
- **Database**: PostgreSQL (via PDO with pdo_pgsql)
- **Frontend**: Vanilla HTML, CSS, JavaScript (no build tools)
- **Server**: PHP built-in server on port 5000

## Project Structure

- `index.php` — Home page entry point
- `pages/` — All site pages (public, member, admin)
  - `pages/admin/` — Admin back-office pages
- `assets/php/` — PHP utilities and backend logic
  - `assets/php/config/` — Configuration files (db.php, auth.php)
  - `assets/php/components/` — Reusable components (header, footer)
  - `assets/php/traitement/` — Form processing handlers
- `assets/sql/init.sql` — Database schema (MySQL format, reference only)
- `css/` — Stylesheets (per-page CSS)
- `js/` — JavaScript files (form validation, etc.)
- `img/` — Images

## Database

Uses Replit's built-in PostgreSQL database. The schema is initialized via `code_execution` (see db.php for connection config using PGHOST/PGPORT/PGDATABASE/PGUSER/PGPASSWORD env vars).

### Tables
- `utilisateurs` — User accounts (roles: visiteur, capitaine, admin)
- `tournois` — Tournaments
- `reservations` — Team tournament registrations
- `contacts` — Messages submitted via contact form

### Default Admin Account
- Email: `admin@gamingcampus.fr`
- Password: `Admin1234!`

## Running

The app runs via PHP's built-in server:
```
php -S 0.0.0.0:5000 -t /home/runner/workspace
```

## Key Notes

- Originally written for MySQL; adapted to PostgreSQL for Replit
- No build system required — pure PHP/HTML/CSS/JS
- Session-based authentication with CSRF protection
- All SQL string literals use single quotes (PostgreSQL strict ANSI)
- `cookie_secure` is auto-detected based on HTTPS context
- Admin pages protected with `gc_require_admin()`, member pages with `gc_require_login()`
- Avatar uploads: extension is derived from validated MIME type (not client filename)
- Tournament filters submit via GET to the server (full DB filtering, not just current page)

## Bug Fixes (cf. RAPPORT_TESTS.md)

All 16 bugs identified in the test report have been fixed. Detailed list of changes:
1. SQL double quotes → single quotes (9 files, ~25 occurrences)
2. Admin pages now use `gc_require_admin()` instead of `gc_require_login()`
3. Contact form persists messages to new `contacts` table
4. `admin/inscriptions.php` ORDER BY uses whitelist (no SQL injection)
5. `tournois-filtres.js` no longer prevents server-side form submission
6. Avatar upload paths verified consistent
7. Forgot-password link disabled (no implementation yet)
8. Cookie Secure flag now HTTPS-aware
9. `favicon.ico` added at project root
10. `profil.php` requires login
11. Classement separates en-attente/annulee from defaites
12. Avatar file extension comes from MIME, not client filename
13. + PostgreSQL `FIELD()` → `CASE` in tournois.php
14. + GROUP BY columns added in classement.php for PG strict mode

## Pushing to `debug_final` branch

After Replit checkpoints (auto-commits) the changes, run:
```
./push_debug_final.sh
```
You must first connect Replit to GitHub via the **Git panel → Connect to GitHub** button so that `git push` is authenticated. The script creates a `debug_final` branch from the current HEAD and pushes it to GitHub without touching `main`.
