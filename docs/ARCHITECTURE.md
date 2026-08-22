# Phase 1 Architecture

## Core domains
- Tenancy
- Identity
- Organization
- Authorization
- Modules
- Audit

## Request flow
`HTTP -> middleware -> FormRequest -> Controller -> Application Action -> Model/Domain -> Resource -> JSON`

## Tenant resolution
For authenticated tenant routes, the active tenant is derived from the authenticated user. Client-provided tenant IDs are never trusted to select the data boundary.

Platform administrators are separate users with `tenant_id = null` and use `/api/v1/platform/*` endpoints.

## Data isolation
Tenant-owned tables carry `tenant_id` and use the `BelongsToTenant` scope. Tests verify cross-tenant access returns 404/403.

## Organization vs Tenant
`Tenant` is the technical SaaS boundary. `Organization` is business identity. Phase 1 is one primary organization per tenant, without hard-coding this assumption into the tenant model.

## Modules
Global module definitions live in `modules`; activations live in `tenant_modules`. Business modules may be enabled per tenant without changing the core.
