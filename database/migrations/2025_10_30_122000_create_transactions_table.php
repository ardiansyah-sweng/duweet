<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    protected string $table;
<<<<<<< HEAD
    protected string $financialAccountTable;
    protected string $userAccountTable;

    public function __construct()
    {
    // table names are stored in config/db_tables.php
    $this->table = config('db_tables.transaction');
    $this->financialAccountTable = config('db_tables.financial_account');
    $this->userAccountTable = config('db_tables.user_account');
=======
    protected string $userAccountTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        // table names are stored in config/db_tables.php
        $this->table = config('db_tables.transaction');
        $this->userAccountTable = config('db_tables.user_account');
        $this->financialAccountTable = config('db_tables.financial_account');
>>>>>>> efc72c84f81e7c2b35b1c4b61e0e57b691daa99f
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Grouping id to tie debit-credit pairs
            $table->string(TransactionColumns::TRANSACTION_GROUP_ID, 36)->nullable(false)->index();

            // References to user_accounts and financial_accounts
            $table->unsignedBigInteger(TransactionColumns::USER_ACCOUNT_ID)->nullable(false)->index();
            $table->unsignedBigInteger(TransactionColumns::FINANCIAL_ACCOUNT_ID)->nullable(false)->index();
=======
        // Step 1: Create table structure first
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Foreign key columns (without constraints yet)
            $table->unsignedBigInteger(TransactionColumns::USER_ACCOUNT_ID)->index();
            $table->unsignedBigInteger(TransactionColumns::FINANCIAL_ACCOUNT_ID)->index();

            // Grouping id to tie debit-credit pairs
            $table->string(TransactionColumns::TRANSACTION_GROUP_ID, 36)->index();
>>>>>>> efc72c84f81e7c2b35b1c4b61e0e57b691daa99f

            // Transaction details
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->bigInteger(TransactionColumns::AMOUNT)->default(0);
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->text(TransactionColumns::DESCRIPTION)->nullable();
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            $table->timestamps();

<<<<<<< HEAD
            // Foreign keys
        $table->foreign(TransactionColumns::USER_ACCOUNT_ID)
            ->references('id')
            ->on($this->userAccountTable)
            ->onDelete('cascade');

            $table->foreign(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                  ->references('id')
                  ->on($this->financialAccountTable)
                  ->onDelete('cascade');

            // Additional indexes for common filters
            $table->index([TransactionColumns::ENTRY_TYPE, TransactionColumns::CREATED_AT]);
=======
            // Composite index for filtering
            $table->index([TransactionColumns::ENTRY_TYPE, TransactionColumns::CREATED_AT]);
        });

        // Step 2: Add foreign key constraints after table exists
        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign(TransactionColumns::USER_ACCOUNT_ID)
                ->references('id')
                ->on($this->userAccountTable)
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                ->references('id')
                ->on($this->financialAccountTable)
                ->restrictOnDelete()
                ->cascadeOnUpdate();
>>>>>>> efc72c84f81e7c2b35b1c4b61e0e57b691daa99f
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
