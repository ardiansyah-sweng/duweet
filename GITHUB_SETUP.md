# GitHub Setup Instructions

Ikuti langkah-langkah berikut untuk push project ke GitHub:

## 1. Buat Repository di GitHub

1. Buka [GitHub.com](https://github.com)
2. Login ke akun GitHub Anda
3. Klik tombol "+" di pojok kanan atas, pilih "New repository"
4. Isi form:
   - **Repository name**: `duweet`
   - **Description**: `Personal Finance App - Aplikasi keuangan pribadi berbasis gamifikasi dengan Laravel`
   - **Visibility**: Public (atau Private sesuai kebutuhan)
   - **DON'T** initialize with README, .gitignore, atau license (karena sudah ada)
5. Klik "Create repository"

## 2. Connect Local Repository ke GitHub

Setelah repository dibuat, GitHub akan menampilkan instruksi. Jalankan commands berikut di terminal:

```bash
# Add remote origin (ganti USERNAME dengan username GitHub Anda)
git remote add origin https://github.com/USERNAME/duweet.git

# Rename branch ke main (opsional, modern standard)
git branch -M main

# Push ke GitHub
git push -u origin main
```

## 3. Verify Upload

1. Refresh halaman repository di GitHub
2. Pastikan semua files sudah terupload
3. Check README.md tampil dengan baik
4. Verify bahwa .env tidak terupload (karena ada di .gitignore)

## 4. Setup Repository Settings (Opsional)

### Add Topics/Tags
Di halaman repository, klik ⚙️ (Settings) > About > Topics, tambahkan:
- `laravel`
- `php`
- `personal-finance`
- `mysql`
- `hierarchical-data`
- `financial-management`

### Enable Issues dan Discussions
Di Settings > Features:
- ✅ Issues
- ✅ Discussions (untuk Q&A komunitas)

### Branch Protection (untuk Tim)
Di Settings > Branches:
- Add rule untuk `main` branch
- Require PR reviews
- Require status checks

## 5. Clone URL untuk Developer Lain

Setelah di push, developer lain bisa clone dengan:
```bash
git clone https://github.com/USERNAME/duweet.git
cd duweet
composer install
cp .env.example .env
php artisan key:generate
# Setup database sesuai README.md
php artisan migrate:fresh --seed
```

## 6. Update README

Setelah push, update README.md di bagian clone URL:
```markdown
git clone https://github.com/YOUR-USERNAME/duweet.git
```

## Commands Summary

```bash
# 1. Add remote (ganti USERNAME)
git remote add origin https://github.com/USERNAME/duweet.git

# 2. Rename branch
git branch -M main

# 3. Push ke GitHub
git push -u origin main

# 4. Untuk push selanjutnya (setelah ada perubahan)
git add .
git commit -m "your commit message"
git push
```

## Troubleshooting

### Authentication Error
Jika diminta username/password saat push:
1. Gunakan Personal Access Token sebagai password
2. Atau setup SSH key untuk authentication

### Repository Already Exists Error
Jika error "repository already exists":
1. Pastikan repository di GitHub kosong
2. Atau gunakan force push: `git push -f origin main` (HATI-HATI)

### Permission Denied
Pastikan Anda adalah owner atau collaborator dari repository.

---

Setelah mengikuti langkah-langkah di atas, project Duweet akan tersedia di GitHub dan siap untuk collaboration! 🚀
