# Accounts Count

This document explains how to migrate and populate the `accounts_count` column on the `users` table and how to keep it in sync.

Files added:
- `database/migrations/2025_11_15_000001_add_accounts_count_to_users_table.php` (adds `accounts_count` column)
- `app/Constants/UserColumns.php` (new constant: `ACCOUNTS_COUNT`)
- `app/Console/Commands/AccountsRecount.php` (artisan command `accounts:recount`)
- `app/Console/Kernel.php` (registers the command)

Run migration

```powershell
php artisan migrate
```

Populate `accounts_count` (recommended)

```powershell
php artisan accounts:recount
```

The command does a DB-agnostic recount using chunked user iteration and updates the `users.accounts_count` column efficiently.

Quick Eloquent query examples

- Get users with account counts (live, without using denormalized column):

```php
$users = \App\Models\User::withCount('userAccounts')->get();
```

- Raw SQL report:

```sql
SELECT u.id, u.name, COUNT(ua.id) AS accounts_count
FROM users u
LEFT JOIN user_accounts ua ON ua.id_user = u.id
GROUP BY u.id, u.name;
```

Notes
- The migration uses default `'users'` table name when executed outside Laravel helper context; Laravel will still use the configured table name.
- You can schedule `accounts:recount` in `app/Console/Kernel.php` if you want periodic recalculation.
