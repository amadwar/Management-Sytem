<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Module extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'name', 'description', 'is_core', 'is_active'];

    protected function casts(): array
    {
        return ['is_core' => 'boolean', 'is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function activations(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }
}
