<p align="center">
  <img src="public/logo.svg" alt="Open Docket" width="80">
</p>

# Open Docket

A ticket management system for tracking issues, managing contacts, and coordinating support workflows. Built with Laravel 12.

> **Work in progress** — The application is still under active development. Minor bugs may be present and adjustments to defaults (such as built-in ticket types, statuses, and other configuration) may change without notice.

## Features

- **Ticket management** — Create and track tickets with configurable statuses, priorities, and SLA days
- **Custom ticket types** — Define types with custom schemas, icons, colors, and allowed status transitions
- **Contacts** — Manage contact records with organization, designation, and communication details
- **Comments & attachments** — Add internal or public comments with file attachments to tickets
- **Reminders** — Schedule one-off or recurring reminders on tickets with notifications
- **Activity log** — Full audit trail of all changes across tickets, contacts, and types
- **Tags** — Organize tickets with a shared tag system

## Deployment

### Prerequisites

- [Docker](https://www.docker.com/get-started) with Docker Compose
- **Database** — MySQL 8.0+ or PostgreSQL 15+
- **Redis** — required only if using [Laravel Horizon](https://laravel.com/docs/horizon) for queue processing
- **Object storage** — S3-compatible bucket (optional; falls back to local private disk)

### Quick start

A reference Compose file is provided at [`docker-compose.prod.yml`](docker-compose.prod.yml). It pulls the pre-built image from GHCR and starts the app, scheduler, queue worker, and a MySQL database.

1. Create your environment file:

```bash
cp .env.example .env
```

2. Configure the required variables in `.env`:

```dotenv
APP_NAME="Open Docket"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=docket
DB_USERNAME=docket
DB_PASSWORD=<secure-password>

# Queue — use "database" (default) or "redis" (if running Horizon)
QUEUE_CONNECTION=database

# Redis — only needed with Horizon
# REDIS_HOST=redis
# REDIS_PORT=6379

# S3 — optional, omit to use local private storage
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
```

3. Start the services:

```bash
docker compose -f docker-compose.prod.yml up -d
```

4. Generate the application key and run migrations:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan key:generate
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

The app will be available at `http://localhost` (or the port set via `APP_PORT`).

### Using Horizon (optional)

To use Horizon for queue processing instead of the default queue worker:

1. Uncomment the `redis` and `horizon` services in `docker-compose.prod.yml`
2. Uncomment the `redis-data` volume
3. Set `QUEUE_CONNECTION=redis` and configure `REDIS_HOST=redis` in `.env`
4. Remove or stop the default `queue` service — Horizon replaces it

### Using S3 storage (optional)

Set `FILESYSTEM_DISK=s3` and provide your AWS/S3-compatible credentials in `.env`. When omitted, file attachments are stored on the local private disk inside the `storage` volume.

### Image tags

Images are published to `ghcr.io/sourcedopen/docket`. Available tags:

| Tag | Description |
|---|---|
| `latest` | Most recent stable release |
| `x.y.z` | Specific version |

---

## Local Development

### Requirements

- PHP 8.2+
- Node.js 22+
- Composer

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
- [SourcedOpen Tags](https://github.com/sourcedopen/laravel-tags) — Tagging
- [Pest](https://pestphp.com) — Testing

## License

Open Docket is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
