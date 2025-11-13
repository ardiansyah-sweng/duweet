<?php

use App\Constants\TransactionsColums;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected string $table;

    public function __construct(){

    $this->table = config('db_tables.transactions');
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            
            // UUID untuk grup transaksi (pasangan debit-kredit)
             $table->uuid(TransactionsColums::TRANSACTION_GROUP_ID)->index();
            
            // Siapa yang mencatat
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Akun mana yang terpengaruh
            $table->foreignId('account_id')->constrained('financial_accounts')->onDelete('cascade');
            
            $table->enum(TransactionsColums::ENTRY_TYPE, ['debit', 'credit']);

            $table->unsignedBigInteger(TransactionsColums::AMOUNT);

            $table->enum(TransactionsColums::BALANCE_EFFECT, ['increase', 'decrease']);

            $table->text(TransactionsColums::DESCRIPTION)->nullable();

            $table->boolean(TransactionsColums::IS_BALANCE)->default(false);

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