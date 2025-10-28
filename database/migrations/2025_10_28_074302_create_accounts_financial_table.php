<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();

            // Parent account (nullable, self-referential FK)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('financial_accounts')
                ->nullOnDelete();

            // Core attributes
            $table->string('name', 100);
            $table->enum('type', ['IN', 'EX', 'SP', 'LI', 'AS']); // Income, Expense, Saving, Liability, Asset
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('color', 10)->nullable(); // e.g. "#FF0000"
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('level')->default(0);

            $table->timestamps();

            // Validation-related constraints
            $table->unique(['parent_id', 'name']); // name unique per parent (per level)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
