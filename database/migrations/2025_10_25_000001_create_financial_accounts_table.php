<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.account', 'financial_accounts');
    }

    public function up(): void
    {
        if (Schema::hasTable($this->table)) return;

        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Kode akun: AS, BK, INV, dst.');
            $table->string('name', 100)->comment('Nama akun: Kas, Bank, Piutang, dst.');
            $table->enum('type', ['AS', 'LI', 'EQ', 'RV', 'EX'])
                  ->comment('AS=Asset, LI=Liability, EQ=Equity, RV=Revenue, EX=Expense');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
