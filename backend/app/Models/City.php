<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class City extends Model
{
    protected $fillable = ['country_id','name_en','name_ar','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; }
}
