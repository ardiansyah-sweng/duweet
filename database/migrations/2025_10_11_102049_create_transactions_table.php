<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_group_id')->index();
            $table->foreignId('user_account_id')->constrained('user_accounts')->onDelete('cascade');
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->onDelete('restrict');
            $table->enum('entry_type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->string('description')->nullable();
            $table->boolean('is_balance')->default(false);
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};