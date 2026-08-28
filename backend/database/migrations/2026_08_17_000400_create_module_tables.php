<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $t) {
            $t->id();
            $t->string('code', 100)->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->boolean('is_core')->default(false);
            $t->boolean('is_active')->default(true);
        });
        Schema::create('tenant_modules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('module_id')->constrained()->cascadeOnDelete();
            $t->boolean('enabled')->default(false);
            $t->jsonb('configuration')->default('{}');
            $t->timestamps();
            $t->unique(['tenant_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('modules');
    }
};
