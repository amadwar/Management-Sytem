<?php
use App\Domain\Tenancy\TenantContext; use App\Models\Tenant;
it('stores and clears the current tenant', function () { $context=new TenantContext(); $tenant=new Tenant(['slug'=>'x']); $tenant->id=99; $context->set($tenant); expect($context->has())->toBeTrue()->and($context->id())->toBe(99); $context->clear(); expect($context->has())->toBeFalse(); });
