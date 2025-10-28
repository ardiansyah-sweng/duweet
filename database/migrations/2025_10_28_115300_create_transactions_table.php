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
        Schema::create('transactions', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('transactions_group_id', 100);
            $table->integer('user_account_id')->unique('user_account_id');
            $table->integer('financial_account_id')->unique('financial_account_id');
            $table->enum('entry_type', ['debit', 'credit']);
            $table->bigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->integer('description');
            $table->boolean('is_balance');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
