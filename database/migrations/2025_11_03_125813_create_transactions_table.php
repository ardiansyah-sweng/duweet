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
        // Sesuai PRD Anda
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        Schema::create('transactions', function (Blueprint $table) use ($accountsTable) {
            $table->id();
            $table->uuid('transaction_group_id')->index();
            $table->foreignId('user_account_id')->constrained('user_accounts')->onDelete('cascade');
            
            // Foreign key ke financial_accounts
            $table->unsignedBigInteger('financial_account_id');
            $table->foreign('financial_account_id')->references('id')->on($accountsTable)->onDelete('cascade');
            
            $table->enum('entry_type', ['debit', 'credit']);
            $table->bigInteger('amount'); // Selalu positif
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->string('description');
            $table->boolean('is_balance')->default(false);
            
            // PENTING: Dibutuhkan oleh Seeder dan Query Anda
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
