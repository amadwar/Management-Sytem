<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->uuid('public_id')->unique();

            $table->string('type', 20);

            $table->string('sku', 100)->nullable();

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal('price', 18, 4)->default(0);

            $table->string('currency_code', 3);

            $table->string('unit', 50)->nullable();

            $table->string('status', 20)->default('active');

            $table->boolean('taxable')->default(false);

            $table->timestamps();

            $table->index([
                'tenant_id',
                'type',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);

            $table->index([
                'tenant_id',
                'name',
            ]);

            $table->unique([
                'tenant_id',
                'sku',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
