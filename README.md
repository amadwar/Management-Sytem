# MDR Business Platform — Phase 1

Production-oriented SaaS core for a general business platform.

## Stack
- Laravel 13 / PHP 8.3+
- Angular 22
- PostgreSQL 18
- Redis 7.4
- Docker Compose for local infrastructure

## Phase 1 capabilities
- Multi-tenant SaaS core
- Platform Super Admin
- Tenant / Organization management
- Company users
- Branches
- Custom RBAC (roles + permissions)
- Modules + tenant module activation
- Audit log
- API v1
- Angular web shell
- Arabic / English UI direction support
- Automated backend tests

## Architecture rules
1. Tenant isolation is enforced server-side.
2. Controllers are thin. Use-cases live in Application Actions.
3. DTOs and Enums remove magic arrays/strings.
4. API Resources own serialization.
5. Platform users have `tenant_id = null`; tenant users always belong to one tenant.
6. The SaaS core is industry-neutral. Delivery/logistics is a later optional module.

## Quick start

### 1. Infrastructure
```bash
docker compose up -d
```

Creates:
- PostgreSQL dev DB: `business_saas`
- PostgreSQL test DB: `business_saas_testing`
- Redis

### 2. Backend
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 3. Backend tests
```bash
cd backend
cp .env.testing.example .env.testing
php artisan test
```

### 4. Frontend
```bash
cd frontend
npm install
npm start
```

Open `http://localhost:4200`.

## Initial platform admin
Configure before seeding:
- `PLATFORM_ADMIN_NAME`
- `PLATFORM_ADMIN_EMAIL`
- `PLATFORM_ADMIN_PASSWORD`

Never commit real secrets.

## Tenant login
Tenant users sign in with:
- workspace slug
- email
- password

This avoids ambiguity when the same email exists in multiple tenants.
