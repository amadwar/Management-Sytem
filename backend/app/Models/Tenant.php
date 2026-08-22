<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Tenancy\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['public_id', 'slug', 'status', 'default_locale', 'timezone'];

    protected function casts(): array
    {
        return ['status' => TenantStatus::class];
    }

    public function getRouteKeyName(): string { return 'public_id'; }

    public function organization(): HasOne { return $this->hasOne(Organization::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function branches(): HasMany { return $this->hasMany(Branch::class); }
    public function roles(): HasMany { return $this->hasMany(Role::class); }
    public function moduleActivations(): HasMany { return $this->hasMany(TenantModule::class); }
}
