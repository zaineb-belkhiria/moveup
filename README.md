# MoveUp

A fitness tracking web app (PHP MVC + MySQL): workouts, progression, weight, nutrition, water, courses and gym-owner dashboards.

## Requirements
- PHP 8+ and MySQL/MariaDB (XAMPP works)

## Setup
1. Put the project in your web root (e.g. `htdocs/moveup28`).
2Create a MySQL database named moveup and import schema.sql (phpMyAdmin → Importer).
3. Check `config/database.php` (defaults: user `root`, empty password).
4. Point Apache at the `public/` folder (or open `http://localhost/moveup/public`).
5. Optional, for password-reset emails: copy `config/mail.example.php` to `config/mail.php` and add your Gmail App Password.

## Structure
- `app/` controllers, models, views, services
- `core/` router, view, auth, database
- `public/` entry point, CSS, JS, assets
## 📸 Screenshots

### Admin Dashboard
![Admin Dashboard](screenshots/dashboard_admin1.png)

### User Dashboard
![User Dashboard](screenshots/dashboard_user.png)
![User Dashboard 2](screenshots/dashboard_user1.png)

### Exercises
![Exercises](screenshots/exercices.png)

### Gym - Add
![Add Gym](screenshots/gym_add.png)

### Gym Dashboard
![Gym Dashboard](screenshots/gym_dashboard.png)

### Admin Messages
![Admin Messages](screenshots/messages_admin.png)

### User Progression
![User Progression](screenshots/progression_user.png)

### Reviews
![Reviews](screenshots/reviews.png)