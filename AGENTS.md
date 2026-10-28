# KREASI RPL Development Guidelines

Platform digital SMK Rekayasa Perangkat Lunak (RPL) untuk Project Based Learning (PjBL), manajemen proyek siswa, bimbingan laporan PKL kelas XII, dan portofolio digital.

## Tech Stack & Architecture

- **Backend:** Laravel 12 (PHP 8.3+), Eloquent ORM, MySQL (testing on SQLite `:memory:`).
- **Frontend:** Blade templates, Livewire 4, Alpine.js, Tailwind CSS 4 (`@tailwindcss/vite`).
- **Core Packages:** `spatie/laravel-permission`, `maatwebsite/excel`.
- **PWA:** Manifest and offline fallback at `/offline`.
- **Role Hierarchy / Auth:** Handled via custom middleware `role` (`App\Http\Middleware\CheckRole`).
  - Roles: `admin`, `guru`, `siswa`, `ketua_kelompok`.
  - In route middleware `role:siswa`, users with `ketua_kelompok` role are automatically authorized.
  - Login accepts email, student NIS, or teacher NIP/teacher_number.

## Developer Commands

### Environment Setup
```sh
# Copy environment file and generate key (if .env does not exist)
copy .env.example .env
php artisan key:generate

# Install dependencies
composer install
npm install
```

### Dev Server & Asset Build
```sh
# Build frontend assets (Vite + Tailwind v4)
npm run build

# Start Vite dev watcher
npm run dev

# Run Laravel dev server
php artisan serve
# Or run concurrently via Composer script:
composer run dev
```

*Note on Windows PowerShell:* If PowerShell restricts scripts (`npm.ps1`), use `cmd.exe /c "npm run <script>"` or configure execution policy.

### Database & Seeders
```sh
# Run migrations with default seeding
php artisan migrate --seed

# Run specific seeders
php artisan db:seed --class=RoleAndPermissionSeeder
php artisan db:seed --class=KreasiInitialSeeder
```

Default seeded accounts (password: `password`):
- Admin: `admin@kreasi.test`
- Guru: `guru@kreasi.test` / NIP `198507122010011002`
- Ketua Kelompok: `ketua@kreasi.test` / NIS `2026120101`
- Siswa: `siswa@kreasi.test` / NIS `2026120102`

## Testing & Quality

### Testing (PHPUnit)
PHPUnit uses in-memory SQLite and fake mail/cache drivers (configured in `phpunit.xml`). Ensure `APP_KEY` exists in `.env` before running tests.

```sh
# Run entire test suite
php artisan test

# Run a single test file
php artisan test tests/Feature/AcademicTest.php
php artisan test tests/Feature/PjblTest.php
php artisan test tests/Feature/PklGroupTest.php
php artisan test tests/Feature/PklTest.php
php artisan test tests/Feature/PwaTest.php

# Run a focused test method
php artisan test --filter=test_student_can_submit_pkl_title
```

### Code Formatting (Laravel Pint)
```sh
# Check styling
./vendor/bin/pint --test

# Fix styling automatically
./vendor/bin/pint
```

## Important Gotchas & Conventions

- **Storage Link:** Uploaded project files, logbook attachments, and PKL chapter PDFs are stored in `storage/app/public`. Run `php artisan storage:link` if asset URLs are broken in local browser previews.
- **Audit Logging:** Sensitive actions (logins, logouts, administrative mutations) invoke `App\Models\AuditLog::record(...)`.
- **No External Single Page App Frameworks:** Do not introduce Vue, React, or separate frontend builds. Keep interactive UI in Blade + Livewire / Alpine.js.
