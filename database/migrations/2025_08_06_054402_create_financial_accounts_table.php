<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table = 'financial_accounts';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 100);
            $table->enum('type', ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->tinyInteger('level')->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('parent_id')
                ->references('id')
                ->on($this->table)
                ->onDelete('cascade');

            // Indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index(['type', 'is_active']);
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
