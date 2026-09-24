# MoveUp

A fitness tracking web app (PHP MVC + MySQL): workouts, progression, weight, nutrition, water, courses and gym-owner dashboards.

## Requirements
- PHP 8+ and MySQL/MariaDB (XAMPP works)

## Setup
1. Put the project in your web root (e.g. `htdocs/moveup28`).
2. Create a MySQL database named `moveup`.
3. Check `config/database.php` (defaults: user `root`, empty password).
4. Point Apache at the `public/` folder (or open `http://localhost/moveup28/public`).
5. Optional, for password-reset emails: copy `config/mail.example.php` to `config/mail.php` and add your Gmail App Password.

## Structure
- `app/` controllers, models, views, services
- `core/` router, view, auth, database
- `public/` entry point, CSS, JS, assets
