# Phase 2 — CRM & Customers

## Goal
Phase 2 turns the SaaS core into a usable business platform by adding a tenant-isolated CRM module that works for companies from different industries.

## Included
- Customers: person/company, status, contact data, tax/website/address/source, assigned user, custom fields foundation.
- Contacts: multiple contact persons per customer, primary contact support.
- Tags: tenant-owned customer categorization.
- Notes: relationship notes with author and timestamps.
- Activities: calls, emails, meetings, tasks and other interactions.
- Leads: pipeline stages, estimated value, source and conversion to customer.
- Search/filter/pagination for customers and leads.
- CRM permissions and Company Owner permission upgrade during seeding.
- Audit events for customer/contact/note/activity/lead changes.
- CRM module activation middleware.
- Angular pages for Customers, Customer Detail and Leads.
- Inline validation, success/error and loading states.
- Feature tests for CRM creation, isolation and module activation.

## CRM permissions
- crm.customers.view
- crm.customers.create
- crm.customers.update
- crm.customers.delete
- crm.tags.manage
- crm.notes.create
- crm.activities.create
- crm.leads.view
- crm.leads.create
- crm.leads.update
- crm.leads.convert

## Upgrade from an existing Phase 1 database
From `backend`:

```powershell
php artisan migrate
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=AuthorizationSeeder
php artisan optimize:clear
php artisan test
```

Then log in as the company owner and enable **CRM** under **Modules**.

For a brand-new database:

```powershell
php artisan migrate --seed
php artisan test
```

## Frontend
From `frontend`:

```powershell
npm install
npm run build
npm start
```

Routes:
- `/crm/customers`
- `/crm/customers/:id`
- `/crm/leads`

## Security decisions
- Every CRM-owned table stores `tenant_id` and uses the shared `BelongsToTenant` scope.
- Client-supplied tenant headers do not control tenant resolution.
- `assigned_to` and tag IDs are validated against the authenticated tenant.
- CRM API routes require the CRM module to be enabled.
- Platform routes require an authenticated platform administrator.
