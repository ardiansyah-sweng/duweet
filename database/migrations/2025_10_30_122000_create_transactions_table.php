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
        // Samakan dengan config/db_tables.php milikmu
        $this->table                 = config('db_tables.transaction');
        $this->userAccountTable      = config('db_tables.user_account');
        $this->financialAccountTable = config('db_tables.financial_account');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // PRD: UUID untuk mengelompokkan debit–credit pair
            $table->uuid('transaction_group_id');

            // PRD: relasi ke user_accounts (bukan users)
            $table->foreignId('user_account_id')
                  ->constrained($this->userAccountTable)
                  ->onDelete('cascade');

            // PRD: relasi ke financial_accounts (single account per record)
            $table->foreignId('financial_account_id')
                  ->constrained($this->financialAccountTable)
                  ->onDelete('restrict');

            // PRD: entry_type & balance_effect
            $table->enum('entry_type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount'); // selalu positif (PRD)
            $table->enum('balance_effect', ['increase', 'decrease']);

            // PRD: deskripsi & status keseimbangan grup
            $table->string('description');
            $table->boolean('is_balance')->default(false);

            $table->timestamps();

            // Indexes untuk performa query laporan/periode
            $table->index('transaction_group_id', $this->table.'_idx_group');
            $table->index(['user_account_id', 'created_at'], $this->table.'_idx_useracct_date');
            $table->index(['financial_account_id', 'created_at'], $this->table.'_idx_fa_date');
            $table->index('entry_type', $this->table.'_idx_entrytype');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
