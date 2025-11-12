<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    protected string $table;
    protected string $userAccountTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->table = config('db_tables.transaction');
        $this->userAccountTable = config('db_tables.user_account');
        $this->financialAccountTable = config('db_tables.financial_account');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // UUID untuk grup transaksi (misalnya debit-kredit pair)
            $table->uuid(TransactionColumns::TRANSACTION_GROUP_ID)->index();

            // Foreign keys
            $table->foreignId(TransactionColumns::USER_ACCOUNT_ID)
                ->constrained($this->userAccountTable)
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId(TransactionColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained($this->financialAccountTable)
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Informasi transaksi
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->unsignedBigInteger(TransactionColumns::AMOUNT)->default(0);
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->text(TransactionColumns::DESCRIPTION)->nullable();
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            // Timestamp
            $table->timestamps();

            // Composite index untuk efisiensi filter
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
