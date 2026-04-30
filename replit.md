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
