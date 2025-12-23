# Copilot Instructions for Duweet

## Project Overview
- **Duweet** is a gamified personal finance app built with Laravel (PHP 8.3+), using MySQL/MariaDB, Composer, and Node.js for asset compilation.
- The codebase follows standard Laravel conventions, but with some project-specific structures and data flows.

## Key Architecture & Patterns
- **Domain Structure:**
  - `app/Constants/`: Centralizes DB column names for models (e.g., `AccountColumns.php`).
  - `app/Enums/`: Contains enums for domain types (e.g., `AccountType.php`).
  - `app/Models/`: Eloquent models for all main entities (Account, User, FinancialAccount, Transaksi, etc).
  - `app/Http/Controllers/`: Handles HTTP requests and business logic.
- **Database:**
  - Migrations in `database/migrations/` (see `*_accounts_table.php`, `*_user_accounts.php`, etc).
  - Seeders in `database/seeders/` and data in `database/data/`.
  - Table names are configured in `config/db_tables.php`.
- **Hierarchical Accounts:**
  - Accounts are structured in up to 3 levels (see README for hierarchy example).
  - Account types: AS (Asset), IN (Income), EX (Expenses), SP (Spending), LI (Liability).

## Developer Workflows
- **Install:**
  - `composer install` (PHP deps), `npm install` (JS assets)
  - Copy `.env.example` to `.env`, set DB credentials, then run `php artisan key:generate`
- **Database:**
  - `php artisan migrate:fresh --seed` (reset & seed DB)
  - `php artisan db:show` (check DB status)
- **Run App:**
  - `php artisan serve` (dev server at http://localhost:8000)
- **Testing:**
  - `php artisan test` (runs all tests)
- **Code Quality:**
  - `./vendor/bin/pint` (format), `./vendor/bin/phpstan analyse` (static analysis)

## Project-Specific Conventions
- **Constants for Columns:** Always use constants from `app/Constants/` for DB column names in models, migrations, and queries.
- **Enums:** Use enums from `app/Enums/` for type safety (e.g., `AccountType`).
- **Seed Data:** Use files in `database/data/` for initial data population.
- **Error Handling:** For "specified key was too long" errors, set `Schema::defaultStringLength(191)` in `AppServiceProvider.php`.

## Integration & Extensibility
- **External dependencies:** Managed via Composer (PHP) and npm (JS assets).
- **Multiple modules:** The `query_edit_transaksi/` subproject mirrors the main structure for isolated development/testing.
- **Configuration:** All table names and key settings are in `config/`.

## Examples
- To add a new account type, update `app/Enums/AccountType.php` and reference it in models/controllers.
- To add a new DB column, update the relevant constants file, migration, and model.

## References
- See `README.md` for full setup, troubleshooting, and workflow details.
- Key files: `app/Constants/`, `app/Enums/`, `app/Models/`, `database/migrations/`, `config/db_tables.php`.

---
For more, see the project README or open an issue for guidance.
