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
        $this->table = config('db_tables.transaction');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->uuid(TransactionColumns::TRANSACTION_GROUP_ID);

            // Foreign Keys
            $table->foreignId(TransactionColumns::USER_ACCOUNT_ID)
                ->constrained(config('db_tables.user_account'))
                ->onDelete('cascade');

            $table->foreignId(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained(config('db_tables.financial_account'))
                ->onDelete('cascade');

            // Informasi transaksi
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->unsignedBigInteger(TransactionColumns::AMOUNT);
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->string(TransactionColumns::DESCRIPTION);
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            // Timestamp
            $table->timestamps();

            // Indexes
            $table->index(TransactionColumns::TRANSACTION_GROUP_ID);
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
