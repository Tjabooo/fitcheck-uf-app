# fitcheck-uf-app

A work-in-progress web app for managing a digital wardrobe ("garderob") and putting outfits together. Built with Laravel 11 and Tailwind; the interface is in Swedish. Originally a school / Ung Foretagsamhet (UF) project.

## Status
Unfinished and not maintained. What exists: registration with e-mail verification, login, password reset, a closet where you add and delete clothing articles, a profile with a profile picture, and a settings page. The outfit generator ("omkladningsrum") has routes and a controller, but `dressMe` is not implemented.

The repository also contains an earlier plain-PHP prototype (`includes/`, `templates/`) that predates the Laravel code and is not wired into it. It expects `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD` and `DB_NAME` constants that are not defined anywhere.

## Requirements
- PHP 8.2+ and Composer
- Node.js and npm
- SQLite (default in `.env.example`) or another database supported by Laravel

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
```

## Run
```bash
composer run dev      # php artisan serve + queue + logs + vite
```
Or run `php artisan serve` and `npm run dev` separately. Tests: `php artisan test`.

Note that the web root of this project is `public_html/` rather than Laravel's default `public/`, and the e-mail driver defaults to `log`, so verification mails are written to the log in development.

## Structure
- `app/`, `routes/`, `database/migrations/`, `resources/`: Laravel application.
- `8.3/`, `docker-compose.yml`: Laravel Sail Docker setup.
- `.todo`: the author's informal task list.

Not built or run here (no PHP available when this README was written).
