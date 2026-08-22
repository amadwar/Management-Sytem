<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class BranchFactory extends Factory
{
    protected $model=Branch::class;
    public function definition():array{return ['public_id'=>(string)Str::uuid(),'name'=>$this->faker->company().' Branch','code'=>$this->faker->unique()->bothify('BR-###'),'is_active'=>true];}
}
