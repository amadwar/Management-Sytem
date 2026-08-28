<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Country extends Model
{
    protected $fillable = ['iso2', 'iso3', 'name_en', 'name_ar', 'phone_code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
