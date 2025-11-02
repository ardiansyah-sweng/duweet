<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;
    protected string $userTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->table                 = config('db_tables.transaction', 'transactions');
        $this->userTable             = config('db_tables.user', 'users');
        $this->financialAccountTable = config('db_tables.account', 'financial_accounts');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // ID grup transaksi (opsional, untuk grouping debit/kredit)
            $table->uuid('transaction_group_id')->nullable();

            // Relasi ke user
            $table->foreignId('user_id')
                ->constrained($this->userTable)
                ->cascadeOnDelete();

            // Relasi ke akun keuangan (harus cocok dengan tabel financial_accounts)
            $table->foreignId('account_id')
                ->references('id')
                ->on('financial_accounts')   // ⬅️ force the correct table
                ->onDelete('restrict');


            // Detail transaksi
            $table->enum('entry_type', ['debit', 'credit']);           // tipe jurnal
            $table->unsignedBigInteger('amount');                      // nominal
            $table->enum('balance_effect', ['increase', 'decrease']);  // efek ke saldo
            $table->string('description', 255)->nullable();
            $table->boolean('is_balance')->default(false);

            $table->timestamps();

            // Indexing untuk performa query
            $table->index(['user_id', 'created_at']);
            $table->index(['account_id', 'created_at']);
            $table->index(['transaction_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
