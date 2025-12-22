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
     * Tabel transactions sesuai PRD
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Transaction Group ID - UUID untuk mengelompokkan debit-credit pair
            $table->uuid(TransactionColumns::TRANSACTION_GROUP_ID);

            // Foreign Keys sesuai PRD
            $table->foreignId(TransactionColumns::USER_ACCOUNT_ID)
                  ->constrained(config('db_tables.user_account', 'user_accounts'))
                  ->onDelete('cascade');

            $table->foreignId(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                  ->constrained(config('db_tables.financial_account', 'financial_accounts'))
                  ->onDelete('restrict'); // Tidak boleh hapus account yang masih ada transaksi

            // Transaction Details sesuai PRD
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->bigInteger(TransactionColumns::AMOUNT); // Selalu positif
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->string(TransactionColumns::DESCRIPTION);
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            $table->timestamps();

            $table->index(TransactionColumns::TRANSACTION_GROUP_ID);
            $table->index([TransactionColumns::USER_ACCOUNT_ID, TransactionColumns::CREATED_AT]);
            $table->index([TransactionColumns::FINANCIAL_ACCOUNT_ID, TransactionColumns::CREATED_AT]);
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
