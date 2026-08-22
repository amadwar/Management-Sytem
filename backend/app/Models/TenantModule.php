<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TenantModule extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','module_id','enabled','configuration'];
    protected function casts(): array { return ['enabled'=>'boolean','configuration'=>'array']; }
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
}
