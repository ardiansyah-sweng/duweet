<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.transaction', 'transactions');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            // Primary Key
            $table->id(TransactionColumns::ID);
            
            // Foreign Keys
            $table->uuid(TransactionColumns::TRANSACTION_GROUP_ID); // UUID untuk grup transaksi
            $table->foreignId(TransactionColumns::USER_ACCOUNT_ID)
                ->constrained(config('db_tables.user_account', 'user_accounts'))
                ->onDelete('cascade');
            $table->foreignId(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained(config('db_tables.financial_account', 'financial_accounts'))
                ->onDelete('cascade');
            
            // Transaction Information
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'kredit']);
            $table->bigInteger(TransactionColumns::AMOUNT); // Nominal transaksi (selalu positif)
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->text(TransactionColumns::DESCRIPTION)->nullable();
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index([TransactionColumns::TRANSACTION_GROUP_ID]);
            $table->index([TransactionColumns::USER_ACCOUNT_ID, TransactionColumns::CREATED_AT]);
            $table->index([TransactionColumns::FINANCIAL_ACCOUNT_ID, TransactionColumns::ENTRY_TYPE]);
            $table->index([TransactionColumns::IS_BALANCE]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table);
        Schema::enableForeignKeyConstraints();
    }
};
