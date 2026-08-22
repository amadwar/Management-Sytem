# API v1 — Phase 1

Base URL: `http://localhost:8000/api/v1`

## Tenant authentication
- `POST /auth/login`
- `GET /auth/me`
- `POST /auth/logout`

Login body:
```json
{"workspace":"acme","email":"owner@acme.test","password":"..."}
```

## Platform administration
- `POST /platform/auth/login`
- `GET /platform/auth/me`
- `POST /platform/auth/logout`
- `GET|POST /platform/tenants`
- `GET|PUT /platform/tenants/{public_uuid}`
- `GET|POST /platform/plans`
- `PUT /platform/plans/{public_uuid}`

## Tenant core
- `GET|PUT /organization`
- `GET|POST /branches`
- `GET|PUT|DELETE /branches/{public_uuid}`
- `GET|POST /users`
- `GET|PUT|DELETE /users/{public_uuid}`
- `GET|POST /roles`
- `GET|PUT|DELETE /roles/{public_uuid}`
- `GET /permissions`
- `GET /modules`
- `PUT /modules/{module_code}/activation`
- `GET|PUT /settings`
- `GET /audit-logs`
- `GET /reference-data`

All tenant endpoints derive their tenant boundary from the authenticated user.
