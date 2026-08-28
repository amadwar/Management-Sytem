<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('type', 20); // person | company
            $table->string('status', 20)->default('active');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('secondary_phone', 50)->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->string('website')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('source', 80)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->jsonb('custom_fields')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'email']);
        });

        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->string('job_title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
        });

        Schema::create('crm_tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->string('color', 20)->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('crm_customer_tag', function (Blueprint $table): void {
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('crm_tags')->cascadeOnDelete();
            $table->primary(['customer_id', 'tag_id']);
        });

        Schema::create('crm_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('public_id')->unique();
            $table->text('body');
            $table->timestamps();
            $table->index(['tenant_id', 'customer_id', 'created_at']);
        });

        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('type', 30); // call | email | meeting | task | other
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestampTz('occurred_at');
            $table->timestamps();
            $table->index(['tenant_id', 'customer_id', 'occurred_at']);
        });

        Schema::create('crm_leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('stage', 30)->default('new');
            $table->string('source', 80)->nullable();
            $table->decimal('estimated_value', 18, 2)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestampTz('converted_at')->nullable();
            $table->foreignId('converted_customer_id')->nullable()->constrained('crm_customers')->nullOnDelete();
            $table->timestamps();
            $table->index(['tenant_id', 'stage']);
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_notes');
        Schema::dropIfExists('crm_customer_tag');
        Schema::dropIfExists('crm_tags');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_customers');
    }
};
