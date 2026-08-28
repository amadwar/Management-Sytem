<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Language extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'name', 'native_name', 'direction', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
