$ErrorActionPreference = "Stop"
Write-Host "Starting local infrastructure..."
docker compose up -d
Set-Location backend
if (!(Test-Path .env)) { Copy-Item .env.example .env }
composer install
php artisan key:generate
php artisan migrate --seed
Write-Host "Backend ready. Run: php artisan serve"
Set-Location ../frontend
npm install
Write-Host "Frontend ready. Run: npm start"
