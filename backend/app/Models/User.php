<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Identity\Enums\UserStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable
{
    use BelongsToTenant, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['tenant_id', 'public_id', 'name', 'email', 'phone', 'password', 'status', 'is_platform_admin', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => UserStatus::class,
            'is_platform_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function isPlatformAdmin(): bool
    {
        return $this->is_platform_admin && $this->tenant_id === null;
    }

    public function hasPermission(string $code): bool
    {
        if ($this->isPlatformAdmin()) {
            return true;
        }

        return $this->roles()->whereHas('permissions', fn ($q) => $q->where('code', $code))->exists();
    }
}
