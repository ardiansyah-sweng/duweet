<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionsColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.transactions', 'transactions');
    }

    /**
     * Run the migrations.
     * Tabel transactions sesuai PRD
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Transaction Group ID - UUID untuk mengelompokkan debit-credit pair
            $table->uuid(TransactionsColumns::TRANSACTION_GROUP_ID);

            // Foreign Keys sesuai PRD
            $table->foreignId(TransactionsColumns::USER_ACCOUNT_ID)
                  ->constrained(config('db_tables.user_account', 'user_accounts'))
                  ->onDelete('cascade');

            $table->foreignId(TransactionsColumns::FINANCIAL_ACCOUNT_ID)
                  ->constrained(config('db_tables.financial_account', 'financial_accounts'))
                  ->onDelete('restrict'); // Tidak boleh hapus account yang masih ada transaksi

            // Transaction Details sesuai PRD
            $table->enum(TransactionsColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->bigInteger(TransactionsColumns::AMOUNT); // Selalu positif
            $table->enum(TransactionsColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->string(TransactionsColumns::DESCRIPTION);
            $table->boolean(TransactionsColumns::IS_BALANCE)->default(false);

            $table->timestamps();

            $table->index(TransactionsColumns::TRANSACTION_GROUP_ID);
            $table->index([TransactionsColumns::USER_ACCOUNT_ID, TransactionsColumns::CREATED_AT]);
            $table->index([TransactionsColumns::FINANCIAL_ACCOUNT_ID, TransactionsColumns::CREATED_AT]);
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
