<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AuditLog extends Model
{
    public const UPDATED_AT = null;
    protected $fillable = ['tenant_id','user_id','action','auditable_type','auditable_id','ip_address','user_agent','metadata'];
    protected function casts(): array { return ['metadata'=>'array']; }
}
