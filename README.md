# Duweet - Personal Finance App

Aplikasi personal finance berbasis gamifikasi yang dibangun dengan Laravel.

## 📋 Requirements

Sebelum menginstall aplikasi ini, pastikan sistem Anda memiliki:

- **PHP 8.3+** dengan ekstensi:
  - PDO MySQL
  - Mbstring
  - OpenSSL
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
- **Composer** (Dependency Manager untuk PHP)
- **MySQL 8.0+** atau **MariaDB 10.3+**
- **Node.js 16+** dan **npm** (untuk asset compilation)
- **Git** (untuk cloning repository)

## 🚀 Installation

### Opsi 1: Clone Repository Ini (Recommended)

Jika Anda ingin menggunakan project Duweet yang sudah jadi:

#### 1. Cek dan Install Requirements

##### Cek PHP Version
```bash
php --version
```
Pastikan versi PHP 8.3 atau lebih tinggi.

##### Cek/Install Composer
```bash
composer --version
```

Jika Composer belum terinstall:
- **Windows**: Download dari [getcomposer.org](https://getcomposer.org/download/)
- **macOS**: `brew install composer`
- **Linux**: 
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```

##### Cek/Install MySQL
```bash
mysql --version
```

Jika MySQL belum terinstall:
- **Windows**: Download dari [MySQL Downloads](https://dev.mysql.com/downloads/mysql/)
- **macOS**: `brew install mysql`
- **Linux**: `sudo apt install mysql-server` (Ubuntu/Debian)

#### 2. Clone Repository
```bash
git clone https://github.com/ardiansyah-sweng/duweet.git
cd duweet
```

#### 3. Install Dependencies
```bash
composer install
```

#### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Database Configuration

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=duweet
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

#### 6. Create Database
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE duweet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

#### 7. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

#### 8. Start Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di: http://localhost:8000

### Opsi 2: Install Laravel Baru dan Setup Manual

Jika Anda ingin membuat project Laravel baru dan mengimplementasikan struktur Duweet:

#### 1. Install Laravel via Composer
```bash
# Install Laravel installer globally
composer global require laravel/installer

# Atau create project langsung
composer create-project laravel/laravel duweet

cd duweet
```

#### 2. Konfigurasi Database
```bash
# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Edit .env untuk MySQL
```

#### 3. Buat Migration Accounts
```bash
php artisan make:migration create_accounts_table
```

#### 4. Setup Struktur Accounts Manual
- Salin struktur migration dari `database/migrations/2025_08_06_054402_create_accounts_table.php`
- Salin AccountType enum dari `app/Enums/AccountType.php`  
- Salin AccountColumns constants dari `app/Constants/AccountColumns.php`
- Salin account data dari `database/data/accounts_data.php`
- Salin AccountSeeder dari `database/seeders/AccountSeeder.php`

#### 5. Run Migration
```bash
php artisan migrate:fresh --seed
```

## � Laravel Installation Basics

Jika Anda baru dengan Laravel, berikut adalah panduan dasar:

### Install PHP (Windows)
1. Download PHP dari [php.net/downloads](https://www.php.net/downloads.php)
2. Extract ke `C:\php`
3. Add `C:\php` ke PATH environment variable
4. Buat file `php.ini` dari `php.ini-development`
5. Enable extensions yang dibutuhkan:
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   extension=curl
   extension=fileinfo
   ```

### Install Composer
1. Download dari [getcomposer.org](https://getcomposer.org/download/)
2. Run installer dan ikuti petunjuk
3. Verify: `composer --version`

### Laravel Project Types
- **New Project**: `composer create-project laravel/laravel project-name`
- **Clone Existing**: `git clone repository-url`
- **Laravel Installer**: `composer global require laravel/installer`

### Basic Laravel Commands
```bash
# Create new Laravel project
composer create-project laravel/laravel my-project

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve

# Clear cache
php artisan cache:clear
php artisan config:clear

# Make new controller
php artisan make:controller YourController

# Make new model
php artisan make:model YourModel

# Make new migration
php artisan make:migration create_your_table
```

### �🗂️ Database Structure

### Accounts Table
Aplikasi menggunakan struktur hierarchical accounts dengan 3 level maksimal:

```
Level 0 (Root): Cash & Bank, Income, Expenses, Spending, Liability
├── Level 1: Cash, Bank Account, E-Wallet, etc.
└── Level 2: Dompet, BCA Tabungan, GoPay, etc.
```

### Account Types
- **AS (Asset)**: Cash, Bank, E-Wallet, dll
- **IN (Income)**: Salary, Bonus, Freelance, dll  
- **EX (Expenses)**: Housing, Transportation, Food, dll
- **SP (Spending)**: Shopping, Entertainment, Travel, dll
- **LI (Liability)**: Credit Card, Loans, Mortgage, dll

## 🛠️ Development

### Run Tests
```bash
php artisan test
```

### Code Quality
```bash
# Format code
./vendor/bin/pint

# Static analysis  
./vendor/bin/phpstan analyse
```

### Database Operations
```bash
# Fresh migration (WARNING: will delete all data)
php artisan migrate:fresh --seed

# Rollback migrations
php artisan migrate:rollback

# Check database status
php artisan db:show
```

## 📁 Project Structure

```
duweet/
├── app/
│   ├── Constants/           # Database column constants
│   ├── Enums/              # Account type enums
│   ├── Models/             # Eloquent models
│   └── Http/               # Controllers, middleware, requests
├── database/
│   ├── data/               # Seed data files
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── docs/                   # Project documentation
│   └── prd.md             # Product Requirements Document
└── config/                 # Configuration files
```

## 🔧 Configuration

### Database Tables Config
File `config/db_tables.php` berisi konfigurasi nama tabel:
```php
return [
    'account' => 'accounts',
    // add more tables here
];
```

### Account Types Enum
File `app/Enums/AccountType.php` mendefinisikan jenis-jenis account:
```php
enum AccountType: string
{
    case INCOME = 'IN';
    case EXPENSES = 'EX'; 
    case SPENDING = 'SP';
    case LIABILITY = 'LI';
    case ASSET = 'AS';
}
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 🆘 Troubleshooting

### Common Issues

#### 1. "Class 'PDO' not found"
Install PHP PDO extension:
```bash
# Ubuntu/Debian
sudo apt install php8.3-mysql

# CentOS/RHEL
sudo yum install php-pdo php-mysql
```

#### 2. "SQLSTATE[HY000] [2002] Connection refused"
Pastikan MySQL service berjalan:
```bash
# Windows
net start mysql80

# macOS
brew services start mysql

# Linux
sudo systemctl start mysql
```

#### 3. "specified key was too long error"
Tambahkan di `AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

#### 4. Permission Error (Linux/macOS)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 📞 Support

Jika mengalami kesulitan, silakan:
1. Cek dokumentasi di atas
2. Buka issue di GitHub repository
3. Contact: [your-email@example.com]

---
Made with ❤️ for personal finance management

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
