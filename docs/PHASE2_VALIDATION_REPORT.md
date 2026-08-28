# Phase 2 validation report

## Completed in build environment
- PHP syntax validation (`php -l`) across the backend application, migrations, routes, bootstrap and tests: passed.
- CRM tenant-isolation feature tests were added.
- Existing Phase 1 tenant-isolation tests were corrected to grant the permission they intend to test.
- PostgreSQL 18 Docker volume fix and Predis configuration from the corrected Phase 1 baseline are retained.

## Must be run locally after installing dependencies
```powershell
cd backend
composer install
php artisan migrate
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=AuthorizationSeeder
php artisan test

cd ..\frontend
npm install
npm run build
```

The build environment could not complete the npm dependency download within its execution window, so the Angular production build must be verified locally.
