<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false)->index();
            $table->unsignedBigInteger('financial_account_id')->nullable(false)->index();
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('financial_account_id')
                  ->references('id')
                  ->on($this->financialAccountTable)
                  ->onDelete('cascade');

            // Unique constraint: one user can have one record per financial account
            $table->unique(['user_id', 'financial_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_financial_accounts');
    }
};
