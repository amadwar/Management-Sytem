<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Crm\Enums\LeadStage;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CrmLead extends Model
{
    use BelongsToTenant;

    protected $table = 'crm_leads';

    protected $fillable = ['tenant_id', 'public_id', 'name', 'company_name', 'email', 'phone', 'stage', 'source', 'estimated_value', 'currency_code', 'assigned_to', 'notes', 'converted_at', 'converted_customer_id'];

    protected function casts(): array
    {
        return ['stage' => LeadStage::class, 'estimated_value' => 'decimal:2', 'converted_at' => 'datetime'];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'converted_customer_id');
    }
}
