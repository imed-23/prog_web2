# Gaming Campus — Tournament Platform

A gaming tournament management platform for campus students. Mobile-first design with a gaming aesthetic (dark theme, red accents, glassmorphism).

## Tech Stack

- **Backend:** PHP 8.2 (built-in web server)
- **Database:** PostgreSQL (Replit built-in, via PDO with pdo_pgsql)
- **Frontend:** Vanilla HTML5, CSS3, JavaScript — no frameworks
- **Languages:** PHP, SQL, HTML, CSS, JavaScript

## Project Structure

```
/
├── index.php              # Landing page
├── index.html             # Static landing page variant
├── pages/                 # All page files (.php and .html)
│   ├── admin/             # Admin-only pages
│   ├── tournois.php       # Tournaments list
│   ├── inscription.php    # Registration form
│   ├── classement.php     # Rankings
│   └── ...
├── assets/
│   ├── php/
│   │   ├── components/    # Reusable header/footer includes
│   │   ├── config/db.php  # PostgreSQL PDO connection
│   │   └── traitement/    # Form processing handlers
│   └── sql/init.sql       # Original MySQL schema (reference only)
├── css/                   # Page-specific stylesheets
├── js/                    # Client-side scripts
└── img/                   # Static images
```

## Database

Uses Replit's built-in PostgreSQL. Connection credentials come from environment variables: `PGHOST`, `PGPORT`, `PGUSER`, `PGPASSWORD`, `PGDATABASE`.

### Tables
- `utilisateurs` — Users (visiteur, capitaine, admin roles)
- `tournois` — Tournaments
- `reservations` — Team slot bookings

### Default Admin Account
- Email: `admin@gamingcampus.fr`
- Password: `Admin1234!`

## Running the App

The workflow runs: `php -S 0.0.0.0:5000 -t .`

## User Roles

- **Visiteur:** View tournaments and public info
- **Capitaine:** Register teams, manage profile
- **Admin (BDE):** Full management dashboard
