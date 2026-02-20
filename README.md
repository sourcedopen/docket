# Open Docket

A ticket management system for tracking issues, managing contacts, and coordinating support workflows. Built with Laravel 12.

## Features

- **Ticket management** — Create and track tickets with configurable statuses, priorities, and SLA days
- **Custom ticket types** — Define types with custom schemas, icons, colors, and allowed status transitions
- **Contacts** — Manage contact records with organization, designation, and communication details
- **Comments & attachments** — Add internal or public comments with file attachments to tickets
- **Reminders** — Schedule one-off or recurring reminders on tickets with notifications
- **Activity log** — Full audit trail of all changes across tickets, contacts, and types
- **Tags** — Organize tickets with a shared tag system

## Requirements

- PHP 8.2+
- Node.js 22+
- Composer

## Local Setup

### Option 1 — Laravel Herd

[Laravel Herd](https://herd.laravel.com) provides zero-config PHP and Nginx for macOS/Windows.

1. Clone the repo into your Herd sites directory (e.g. `~/Herd/`)
2. Run the setup script:

```bash
composer setup
```

3. Start the development server with all services:

```bash
composer dev
```

The app will be available at `https://docket.test`.

### Option 2 — Docker

Requires [Docker](https://www.docker.com/get-started) with Docker Compose.

1. Copy the environment file:

```bash
cp .env.example .env
```

2. Build and start the container:

```bash
USER_ID=$(id -u) GROUP_ID=$(id -g) docker compose up --build
```

3. In a separate terminal, run the initial setup:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
```

The app will be available at `http://localhost`.

> Set `APP_PORT` in your `.env` to use a different port (default: `80`).

## Development

Start all development services (server, queue, log watcher, Vite):

```bash
composer dev
```

Run the test suite:

```bash
composer test
```

Seed the database with sample data:

```bash
php artisan db:seed
```

## Scheduled Tasks

The following commands run on a schedule and should be registered with your system cron or process manager:

| Command | Schedule | Description |
|---|---|---|
| `php artisan reminders:send` | Every 15 minutes | Sends notifications for due reminders |
| `php artisan tickets:check-overdue` | Daily at 09:00 | Sends notifications for overdue tickets |

Add to cron:

```
* * * * * cd /path/to/docket && php artisan schedule:run >> /dev/null 2>&1
```

## Tech Stack

- [Laravel 12](https://laravel.com) — PHP framework
- [Laravel Fortify](https://laravel.com/docs/fortify) — Authentication
- [Tailwind CSS v4](https://tailwindcss.com) + [daisyUI](https://daisyui.com) — Styling
- [Alpine.js](https://alpinejs.dev) — Interactivity
- [Spatie Activity Log](https://spatie.be/docs/laravel-activitylog) — Audit trail
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) — File attachments
- [Spatie Tags](https://spatie.be/docs/laravel-tags) — Tagging
- [Pest](https://pestphp.com) — Testing

## License

Open Docket is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
