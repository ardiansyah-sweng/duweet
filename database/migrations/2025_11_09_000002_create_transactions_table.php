<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;
    protected string $userAccountTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->table = config('db_tables.transaction', 'transactions');
        $this->userAccountTable = config('db_tables.user_account', 'user_accounts');
        $this->financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            
            // UUID untuk mengelompokkan debit-credit pair
            $table->uuid('transaction_group_id');
            
            // Foreign key ke user_accounts
            $table->foreignId('user_account_id')
                  ->constrained($this->userAccountTable)
                  ->onDelete('cascade');
            
            // Foreign key ke financial_accounts (single account per record)
            $table->foreignId('financial_account_id')
                  ->constrained($this->financialAccountTable)
                  ->onDelete('restrict');
            
            // Jenis entry: debit atau credit
            $table->enum('entry_type', ['debit', 'credit']);
            
            // Nominal (selalu positif)
            $table->bigInteger('amount')->unsigned();
            
            // Efek ke saldo: increase atau decrease
            $table->enum('balance_effect', ['increase', 'decrease']);
            
            // Deskripsi transaksi
            $table->string('description', 255);
            
            // Status grup transaksi: apakah seimbang antara debit dan kredit
            $table->boolean('is_balance')->default(false);
            
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index('transaction_group_id');
            $table->index(['user_account_id', 'financial_account_id']);
            $table->index('entry_type');
            $table->index('is_balance');
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
