<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class CrmTag extends Model
{
    use BelongsToTenant;
    protected $table = 'crm_tags';
    protected $fillable = ['tenant_id','public_id','name','color'];
    public function customers(): BelongsToMany { return $this->belongsToMany(CrmCustomer::class, 'crm_customer_tag', 'tag_id', 'customer_id'); }
}
