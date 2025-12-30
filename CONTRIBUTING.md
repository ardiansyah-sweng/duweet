# Contributing to Duweet

Terima kasih atas minat Anda untuk berkontribusi pada Duweet! Dokumen ini memberikan panduan untuk berkontribusi pada proyek ini.

## Code of Conduct

Dengan berpartisipasi dalam proyek ini, Anda diharapkan untuk mematuhi kode etik kami. Silakan bersikap sopan dan menghormati semua kontributor.

## Cara Berkontribusi

### 1. Fork Repository

```bash
# Fork repository melalui GitHub UI, kemudian clone
git clone https://github.com/your-username/duweet.git
cd duweet
```

### 2. Setup Development Environment

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate:fresh --seed
```

### 3. Create Feature Branch

```bash
# Create dan checkout ke branch baru
git checkout -b feature/nama-fitur-anda

# Atau untuk bugfix
git checkout -b bugfix/nama-bug-yang-diperbaiki
```

### 4. Development Guidelines

#### Code Style
- Ikuti PSR-12 coding standards
- Gunakan `./vendor/bin/pint` untuk format code
- Jalankan `./vendor/bin/phpstan analyse` untuk static analysis

#### Naming Conventions
- **Classes**: PascalCase (`AccountController`)
- **Methods**: camelCase (`createAccount()`)
- **Variables**: camelCase (`$accountType`)
- **Constants**: UPPER_SNAKE_CASE (`ACCOUNT_TYPE`)
- **Database tables**: snake_case (`account_types`)
- **Database columns**: snake_case (`created_at`)

#### Database Guidelines
- Selalu gunakan migrations untuk perubahan database
- Tambahkan foreign key constraints yang diperlukan
- Gunakan index untuk kolom yang sering di-query
- Gunakan constants dari `AccountColumns` class

#### Testing
- Tulis tests untuk fitur baru
- Pastikan semua tests pass: `php artisan test`
- Minimum 80% code coverage untuk fitur baru

### 5. Commit Guidelines

#### Format Commit Message
```
type(scope): description

[optional body]

[optional footer]
```

#### Types
- `feat`: Fitur baru
- `fix`: Bug fix
- `docs`: Perubahan dokumentasi
- `style`: Format code (tidak mengubah logic)
- `refactor`: Refactoring code
- `test`: Menambah atau memperbaiki tests
- `chore`: Maintenance tasks

#### Contoh Commit Messages
```bash
feat(accounts): add hierarchical account structure

- Implement self-referencing foreign key
- Add level-based validation
- Support up to 3 levels of nesting

fix(seeder): resolve foreign key constraint error

docs(readme): update installation instructions

test(accounts): add unit tests for account model
```

### 6. Pull Request Process

#### Sebelum Submit PR
```bash
# Pastikan code sudah formatted
./vendor/bin/pint

# Jalankan tests
php artisan test

# Pastikan no static analysis errors
./vendor/bin/phpstan analyse

# Update dari upstream
git fetch upstream
git rebase upstream/main
```

#### PR Template
Ketika membuat PR, sertakan:

1. **Description**: Jelaskan apa yang diubah dan mengapa
2. **Type of Change**: 
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Breaking change
   - [ ] Documentation update
3. **Testing**: Jelaskan bagaimana fitur/fix ditest
4. **Checklist**:
   - [ ] Code follows style guidelines
   - [ ] Self-review completed
   - [ ] Tests pass
   - [ ] Documentation updated

#### Review Process
- Minimal 1 reviewer approval
- Semua tests harus pass
- No conflicts dengan main branch
- Follow up pada feedback reviewer

## Struktur Proyek

```
duweet/
├── app/
│   ├── Constants/           # Database constants
│   ├── Enums/              # Enum classes
│   ├── Http/
│   │   ├── Controllers/    # Controllers
│   │   ├── Middleware/     # Custom middleware
│   │   └── Requests/       # Form requests
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── data/               # Seed data files
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── docs/                   # Project documentation
├── resources/
│   ├── js/                # Frontend JavaScript
│   ├── css/               # Stylesheets
│   └── views/             # Blade templates
├── routes/                 # Route definitions
└── tests/                 # Test files
    ├── Feature/           # Feature tests
    └── Unit/              # Unit tests
```

## Development Setup

### Requirements
- PHP 8.3+
- Composer
- MySQL 8.0+
- Node.js 16+

### Useful Commands
```bash
# Start development server
php artisan serve

# Watch for file changes
npm run dev

# Run tests
php artisan test

# Run specific test
php artisan test --filter=AccountTest

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check database status
php artisan db:show

# Fresh migration
php artisan migrate:fresh --seed
```

## Issue Reporting

### Bug Reports
Sertakan informasi berikut:
- PHP version
- Laravel version
- Database type dan version
- Steps to reproduce
- Expected vs actual behavior
- Error messages/screenshots

### Feature Requests
Sertakan:
- Use case yang jelas
- Mockup/wireframe jika ada
- Technical requirements
- Potential impact

## Recognition

Kontributor akan diakui dalam:
- CHANGELOG.md
- GitHub contributors list
- Documentation credits

## Questions?

Jika ada pertanyaan tentang contributing:
1. Check existing issues dan discussions
2. Buat issue baru dengan label `question`
3. Contact maintainers

Terima kasih atas kontribusi Anda! 🚀
