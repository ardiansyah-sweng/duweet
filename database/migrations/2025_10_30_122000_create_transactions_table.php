<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    protected string $table = 'transactions';
    protected string $financialAccountTable;

    public function __construct()
    {
        // financial accounts table name is stored in config/db_tables.php
        $this->table = config('db_tables.transaction');
        $this->financialAccountTable = config('db_tables.financial_account);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Grouping id to tie debit-credit pairs
            $table->string(TransactionColumns::TRANSACTION_GROUP_ID, 36)->nullable(false)->index();

            // References to user_accounts and financial_accounts
            $table->unsignedBigInteger(TransactionColumns::USER_ACCOUNT_ID)->nullable(false)->index();
            $table->unsignedBigInteger(TransactionColumns::FINANCIAL_ACCOUNT_ID)->nullable(false)->index();

            // Transaction details
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->bigInteger(TransactionColumns::AMOUNT)->default(0);
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->text(TransactionColumns::DESCRIPTION)->nullable();
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign(TransactionColumns::USER_ACCOUNT_ID)
                  ->references('id')
                  ->on('user_accounts')
                  ->onDelete('cascade');

            $table->foreign(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                  ->references('id')
                  ->on($this->financialAccountTable)
                  ->onDelete('cascade');

            // Additional indexes for common filters
            $table->index([TransactionColumns::ENTRY_TYPE, TransactionColumns::CREATED_AT]);
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
