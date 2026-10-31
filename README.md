# Toko Sumber Urip - Cashier & Sales Recording App

A simple web application for a drinking-water gallon refill shop. It records daily sales, serves customers at the cashier, manages product stock, and shows simple sales trends.

Built with Laravel 13, Blade, Tailwind CSS (via Vite), Alpine.js, and Chart.js.

## Requirements

- PHP 8.3+ (with the `pdo_mysql` extension)
- Composer
- Node.js 18+ and npm
- MySQL (or SQLite for development)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file (if .env does not exist) and generate app key
cp .env.example .env
php artisan key:generate

# 3. Configure the database in .env
#    Set DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    For SQLite, set DB_CONNECTION=sqlite and DB_DATABASE to an absolute path,
#    then create the file: touch database/database.sqlite

# 4. Install frontend dependencies and build assets
npm install
npm run build

# 5. Run migrations and seed sample data
php artisan migrate:fresh --seed
```

## Run the Application

```bash
php artisan serve
# in another terminal (for hot reload during development):
npm run dev
```

Open http://localhost:8000.

## Default Login

| Field | Value |
| --- | --- |
| Username | `admin` |
| Password | `password` |

> **Important:** change the default password after the first login. The app has no user-management UI, so update the password directly through the database (e.g. with `php artisan tinker`) or by editing the seeder and re-seeding.

## Features

- **Dashboard** - today's revenue, transaction count, items sold, 7/30-day sales chart, top products, low-stock warnings.
- **Cashier** - product cards, search, cart with quantity controls, cash/transfer payment, automatic change calculation, printable receipt.
- **Transactions** - filter by date range, search by invoice number, revenue total, detail page, and cancellable transactions (stock is returned automatically).
- **Products** - CRUD for products; products used in transactions can only be deactivated.
- **Stock** - add stock and view the full stock movement history (`masuk`/`keluar`).

## Business Rules

- Checkout is atomic (`DB::transaction`) with row locking. Stock can never go negative.
- Prices always come from the database; the server recalculates the total.
- Cash payments require `paid_amount >= total`; `change_amount` is calculated server-side.
- Products with `track_stock = false` (e.g. *Isi Ulang Air Galon*) never reduce stock.
- Invoice numbers follow `INV-YYYYMMDD-0001` and reset daily.

## Deploying to EthioDeploy

EthioDeploy detects Laravel automatically (from `artisan` + `laravel/framework`) and runs:

- Install: `composer install --no-dev --optimize-autoloader`
- Build: `npm run build` (Vite assets)
- Start: nginx + PHP-FPM with `config:cache`, `route:cache`, `view:cache`

Repo-side config is already prepared:

- `bootstrap/app.php` trusts the platform proxy (`trustProxies(at: '*')`) for HTTPS URLs.
- `config/database.php` reads `DB_URL` and falls back to `DATABASE_URL` (injected by the MySQL addon).

In the EthioDeploy project settings:

1. Add a **MySQL** addon.
2. Set environment variables (see `.env.ethiodeploy.example`):
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<project>.ethiodeploy.app`
   - `APP_KEY=<output of php artisan key:generate --show>`
   - `DB_CONNECTION=mysql`
3. Set the **Start command** so tables exist before the server boots:
   ```bash
   php artisan migrate --force && ethiodeploy-start
   ```
4. If the build fails with `vendor/autoload.php` missing, the install step did not run: clear the build cache and redeploy, and confirm **Settings → Build → Install** is `composer install --no-dev --optimize-autoloader` (the default).

## Exporting the Database (Backup)

For MySQL:

```bash
mysqldump -u <user> -p <database_name> > backup-$(date +%F).sql
```

Restore with:

```bash
mysql -u <user> -p <database_name> < backup-YYYY-MM-DD.sql
```

For SQLite, simply copy `database/database.sqlite`.

## Testing

```bash
php artisan test
```

## Code Style

```bash
vendor/bin/pint
```