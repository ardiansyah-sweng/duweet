<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    protected string $table;
    protected string $accountTable;
    protected string $userTable;

    public function __construct()
    {
        $this->table = config('db_tables.transaction');
        $this->accountTable = config('db_tables.account');
        $this->userTable = 'users';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string(TransactionColumns::TRANSACTION_GROUP_ID, 36);
            $table->foreignId(TransactionColumns::USER_ID)->constrained($this->userTable)->onDelete('cascade');
            $table->foreignId(TransactionColumns::ACCOUNT_ID)->constrained($this->accountTable)->onDelete('restrict');
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);
            $table->bigInteger(TransactionColumns::AMOUNT);
            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);
            $table->string(TransactionColumns::DESCRIPTION);
            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index(TransactionColumns::USER_ID);
            $table->index(TransactionColumns::ACCOUNT_ID);
            $table->index(TransactionColumns::TRANSACTION_GROUP_ID);
            $table->index([TransactionColumns::USER_ID, TransactionColumns::CREATED_AT]);
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
