$ErrorActionPreference = "Stop"
Set-Location backend
if (!(Test-Path .env.testing)) { Copy-Item .env.testing.example .env.testing }
php artisan migrate:fresh --env=testing
php artisan test
