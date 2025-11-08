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
            $table->id();
            $table->unsignedBigInteger('financial_account_id');
            $table->decimal('amount', 15, 2);
            $table->enum('entry_type', ['debit', 'credit']);
            $table->timestamp('transaction_date');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key to financial_accounts
            $table->foreign('financial_account_id')
                ->references('id')
                ->on('financial_accounts')
                ->onDelete('cascade');

            // Indexes for common queries
            $table->index(['transaction_date', 'entry_type']);
            $table->index('financial_account_id');
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