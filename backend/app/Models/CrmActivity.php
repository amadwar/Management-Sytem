<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Crm\Enums\ActivityType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CrmActivity extends Model
{
    use BelongsToTenant;
    protected $table = 'crm_activities';
    protected $fillable = ['tenant_id','customer_id','user_id','public_id','type','subject','description','occurred_at'];
    protected function casts(): array { return ['type' => ActivityType::class, 'occurred_at' => 'datetime']; }
    public function customer(): BelongsTo { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
