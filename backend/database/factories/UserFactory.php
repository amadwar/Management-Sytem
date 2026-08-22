<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Identity\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class UserFactory extends Factory
{
    protected $model=User::class;
    public function definition():array{return ['tenant_id'=>null,'public_id'=>(string)Str::uuid(),'name'=>$this->faker->name(),'email'=>$this->faker->unique()->safeEmail(),'password'=>'password12345','status'=>UserStatus::Active,'is_platform_admin'=>false];}
}
