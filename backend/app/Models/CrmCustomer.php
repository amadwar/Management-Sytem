<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Crm\Enums\CustomerStatus;
use App\Domain\Crm\Enums\CustomerType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CrmCustomer extends Model
{
    use BelongsToTenant;

    protected $table = 'crm_customers';

    protected $fillable = [
        'tenant_id', 'public_id', 'type', 'status', 'name', 'company_name', 'email', 'phone', 'secondary_phone',
        'tax_number', 'website', 'country_id', 'city_id', 'address', 'postal_code', 'source', 'assigned_to',
        'custom_fields', 'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => CustomerType::class,
            'status' => CustomerStatus::class,
            'custom_fields' => 'array',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'customer_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CrmNote::class, 'customer_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'customer_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CrmTag::class, 'crm_customer_tag', 'customer_id', 'tag_id');
    }
}
