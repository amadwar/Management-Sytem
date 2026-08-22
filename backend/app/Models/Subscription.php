<?php

declare(strict_types=1);
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class Subscription extends Model{use BelongsToTenant;protected $fillable=['tenant_id','plan_id','status','trial_ends_at','starts_at','ends_at','auto_renew'];protected function casts():array{return ['trial_ends_at'=>'datetime','starts_at'=>'datetime','ends_at'=>'datetime','auto_renew'=>'boolean'];}public function plan():BelongsTo{return $this->belongsTo(Plan::class);}}
