<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

final class CatalogItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'type',
        'sku',
        'name',
        'description',
        'price',
        'currency_code',
        'unit',
        'status',
        'taxable',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:4',
            'taxable' => 'boolean',
        ];
    }
}
