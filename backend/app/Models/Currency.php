<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Currency extends Model
{
    public $timestamps = false; protected $fillable = ['code','name','symbol','decimal_places','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; }
}
