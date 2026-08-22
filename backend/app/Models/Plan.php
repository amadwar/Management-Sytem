<?php

declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsToMany;use Illuminate\Database\Eloquent\Relations\HasMany;
final class Plan extends Model{protected $fillable=['public_id','name','code','monthly_price','annual_price','currency_code','is_active'];protected function casts():array{return ['monthly_price'=>'decimal:2','annual_price'=>'decimal:2','is_active'=>'boolean'];}public function features():BelongsToMany{return $this->belongsToMany(Feature::class)->withPivot('value');}public function subscriptions():HasMany{return $this->hasMany(Subscription::class);}}
