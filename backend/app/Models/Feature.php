<?php

declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsToMany;
final class Feature extends Model{public $timestamps=false;protected $fillable=['code','name','module_code','type','description'];public function plans():BelongsToMany{return $this->belongsToMany(Plan::class)->withPivot('value');}}
