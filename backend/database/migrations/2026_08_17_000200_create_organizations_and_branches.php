<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->uuid('public_id')->unique();
            $t->string('legal_name');
            $t->string('display_name');
            $t->string('email')->nullable();
            $t->string('phone', 50)->nullable();
            $t->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $t->string('tax_number', 100)->nullable();
            $t->text('address')->nullable();
            $t->timestamps();
            $t->index('tenant_id');
        });
        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->uuid('public_id')->unique();
            $t->string('name');
            $t->string('code', 50)->nullable();
            $t->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $t->text('address')->nullable();
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->string('phone', 50)->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('organizations');
    }
};
