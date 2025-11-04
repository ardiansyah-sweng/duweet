<?php

use App\Constants\TransactionColumns;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected string $table;

    public function __construct(){

    $this->table = config('db_tables.transaction');
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            
            // UUID untuk grup transaksi (pasangan debit-kredit)
             $table->uuid(TransactionColumns::TRANSACTION_GROUP_ID)->index();
            
            // Siapa yang mencatat
            $table->foreignId(TransactionColumns::USER_ID)->constrained('users')->onDelete('cascade');
            
            // Akun mana yang terpengaruh
            $table->foreignId(TransactionColumns::ACCOUNT_ID)->constrained('financial_accounts')->onDelete('cascade');
            
            $table->enum(TransactionColumns::ENTRY_TYPE, ['debit', 'credit']);

            $table->unsignedBigInteger(TransactionColumns::AMOUNT);

            $table->enum(TransactionColumns::BALANCE_EFFECT, ['increase', 'decrease']);

            $table->text(TransactionColumns::DESCRIPTION)->nullable();

            $table->boolean(TransactionColumns::IS_BALANCE)->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transactions');
        Schema::enableForeignKeyConstraints();
    }
};
