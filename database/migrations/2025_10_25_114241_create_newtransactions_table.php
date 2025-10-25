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
        Schema::create('newtransactions', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('transactions_group_id', 100);
            $table->integer('user_id')->unique('user_id');
            $table->integer('account_id')->unique('account_id');
            $table->enum('entry_type', ['debit', 'credit']);
            $table->bigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->string('description', 100);
            $table->boolean('is_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newtransactions');
    }
};
