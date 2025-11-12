# 🔒 Protected Seeders Setup

## Important Notice
The following seeder files are **protected from being pushed** to the remote repository to keep your financial data private:

- `database/seeders/AssetSeeder.php`
- `database/seeders/FinancialAccountSeeder.php` 
- `database/seeders/TransactionSeeder.php`

## 🚀 Quick Setup for New Developers

### 1. Create Your Seeder Files
Copy the template files and rename them:

```bash
# Copy templates to actual seeders
cp database/seeders/AssetSeeder.php.template database/seeders/AssetSeeder.php
cp database/seeders/FinancialAccountSeeder.php.template database/seeders/FinancialAccountSeeder.php
cp database/seeders/TransactionSeeder.php.template database/seeders/TransactionSeeder.php
```

### 2. Implement Your Data
Edit the copied files with your actual financial data:

- **AssetSeeder.php** - Your assets (stocks, property, etc.)
- **FinancialAccountSeeder.php** - Your bank accounts, investment accounts
- **TransactionSeeder.php** - Your transaction history

### 3. Run Seeders
```bash
php artisan migrate:fresh --seed
```

## 🛡️ Security Features

### Pre-commit Hook
- Automatically prevents pushing sensitive files
- Checks for hardcoded secrets
- Can be bypassed with `--no-verify` (not recommended)

### .gitignore Protection  
Files are automatically ignored by git:
```gitignore
database/seeders/AssetSeeder.php
database/seeders/FinancialAccountSeeder.php
database/seeders/TransactionSeeder.php
```

## 🔧 Troubleshooting

### If seeder files accidentally get staged:
```bash
git reset HEAD database/seeders/AssetSeeder.php
git reset HEAD database/seeders/FinancialAccountSeeder.php
git reset HEAD database/seeders/TransactionSeeder.php
```

### If you need to disable security check temporarily:
```bash
git commit --no-verify -m "Your commit message"
```

## 💡 Best Practices

1. **Never commit real financial data** to version control
2. **Use environment variables** for sensitive configuration
3. **Keep backup** of your seeder files locally
4. **Test with sample data** before using real data

---
**Remember: Your financial privacy is protected! 🔐**