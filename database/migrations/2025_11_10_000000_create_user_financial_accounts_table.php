<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('financial_account_id');
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('financial_account_id')->references('id')->on(config('db_tables.financial_account'))->onDelete('cascade');
            $table->index(['user_id', 'financial_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_financial_accounts');
    }
};
