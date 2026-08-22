<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Role extends Model
{
    use HasFactory, BelongsToTenant;
    protected $fillable = ['tenant_id','public_id','name','code','is_system'];
    protected function casts(): array { return ['is_system'=>'boolean']; }
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class)->withTimestamps(); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withTimestamps(); }
}
