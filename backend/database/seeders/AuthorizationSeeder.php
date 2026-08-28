<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'organization' => ['organization.view', 'organization.update'],
            'branches' => ['branches.view', 'branches.create', 'branches.update', 'branches.delete'],
            'users' => ['users.view', 'users.create', 'users.update', 'users.delete'],
            'roles' => ['roles.view', 'roles.create', 'roles.update', 'roles.delete'],
            'modules' => ['modules.view', 'modules.manage'],
            'audit' => ['audit.view'],
            'crm' => [
                'crm.customers.view', 'crm.customers.create', 'crm.customers.update', 'crm.customers.delete',
                'crm.tags.manage', 'crm.notes.create', 'crm.activities.create',
                'crm.leads.view', 'crm.leads.create', 'crm.leads.update', 'crm.leads.convert',
            ],
        ];
        foreach ($groups as $module => $codes) {
            foreach ($codes as $code) {
                Permission::query()->updateOrCreate(['code' => $code], ['name' => ucwords(str_replace(['.', '_'], ' ', $code)), 'module_code' => $module]);
            }
        }

        Role::withoutGlobalScopes()->where('code', 'company_owner')->each(function ($role): void {
            $role->permissions()->syncWithoutDetaching(Permission::query()->pluck('id'));
        });
    }
}
