<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

final class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'organization' => ['organization.view','organization.update'],
            'branches' => ['branches.view','branches.create','branches.update','branches.delete'],
            'users' => ['users.view','users.create','users.update','users.delete'],
            'roles' => ['roles.view','roles.create','roles.update','roles.delete'],
            'modules' => ['modules.view','modules.manage'],
            'audit' => ['audit.view'],
        ];
        foreach($groups as $module=>$codes){foreach($codes as $code){Permission::query()->updateOrCreate(['code'=>$code],['name'=>ucwords(str_replace(['.','_'],' ',$code)),'module_code'=>$module]);}}
    }
}
