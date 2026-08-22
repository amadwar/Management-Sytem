<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

final class ModuleSeeder extends Seeder
{
    public function run():void
    {
        $rows=[
            ['code'=>'core','name'=>'Core','description'=>'Identity, organization, branches, roles and settings.','is_core'=>true,'is_active'=>true],
            ['code'=>'crm','name'=>'CRM','description'=>'Customers, leads and relationships.','is_core'=>false,'is_active'=>true],
            ['code'=>'sales','name'=>'Sales','description'=>'Quotes, orders and invoices.','is_core'=>false,'is_active'=>true],
            ['code'=>'inventory','name'=>'Inventory','description'=>'Products, warehouses and stock.','is_core'=>false,'is_active'=>true],
            ['code'=>'finance','name'=>'Finance','description'=>'Operational finance.','is_core'=>false,'is_active'=>true],
            ['code'=>'hr','name'=>'HR','description'=>'Employees and HR operations.','is_core'=>false,'is_active'=>true],
            ['code'=>'projects','name'=>'Projects','description'=>'Projects and work management.','is_core'=>false,'is_active'=>true],
            ['code'=>'logistics','name'=>'Logistics','description'=>'Delivery, drivers and routing.','is_core'=>false,'is_active'=>true],
            ['code'=>'ecommerce','name'=>'E-Commerce','description'=>'Store integrations.','is_core'=>false,'is_active'=>true],
            ['code'=>'automation','name'=>'Automation','description'=>'Workflows and integrations.','is_core'=>false,'is_active'=>true],
        ];
        Module::query()->upsert($rows,['code'],['name','description','is_core','is_active']);
    }
}
