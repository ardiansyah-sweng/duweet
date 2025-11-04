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
        // Mengambil nama tabel dari konfigurasi (sesuai pola migrasi lain)
        $this->table = config('db_tables.transaction', 'transactions');
        $this->userTable = config('db_tables.user', 'users');
        $this->financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_group_id')->comment('UUID untuk mengelompokkan transaksi terkait');
            
            // relasi ke user dan akun finansial
            $table->foreignId('user_id')
                ->constrained($this->userTable)
                ->onDelete('cascade');

            $table->foreignId('financial_account_id')
                ->constrained($this->financialAccountTable)
                ->onDelete('cascade');

            // tipe transaksi
            $table->enum('entry_type', ['debit', 'credit'])->comment('Jenis entri transaksi');
            $table->bigInteger('amount')->comment('Jumlah nominal transaksi');
            $table->enum('balance_effect', ['increase', 'decrease'])->comment('Efek terhadap saldo');
            $table->string('description')->nullable()->comment('Keterangan transaksi');
            $table->boolean('is_balance')->default(false)->comment('Apakah transaksi merupakan saldo awal');
            
            $table->timestamps();

            // index untuk performa query
            $table->index(['user_id', 'financial_account_id']);
            $table->index('transaction_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
