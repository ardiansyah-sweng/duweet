<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserFinancialAccountColumns;

return new class extends Migration
{
    protected string $table;
    protected string $userTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->table                   = config('db_tables.user_financial_account', 'user_financial_accounts');
        $this->userTable               = config('db_tables.user', 'users');
        $this->financialAccountTable   = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Run the migrations.
     * Tabel relasi antara Users dan Financial_Accounts sesuai PRD
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Foreign Keys sesuai PRD
            $table->foreignId(UserFinancialAccountColumns::ID_USER)
                  ->constrained($this->userTable)
                  ->onDelete('cascade');

            $table->foreignId(UserFinancialAccountColumns::ID_FINANCIAL_ACCOUNT)
                  ->constrained($this->financialAccountTable)
                  ->onDelete('cascade');

            // Kolom keuangan sesuai PRD
            $table->bigInteger(UserFinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            
            // Status
            $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);

            // Index untuk performa
            $table->index([UserFinancialAccountColumns::ID_USER, UserFinancialAccountColumns::IS_ACTIVE]);
            $table->index(UserFinancialAccountColumns::ID_FINANCIAL_ACCOUNT);
            
            // Unique constraint: satu user tidak bisa memiliki financial_account yang sama 2x
            $table->unique([UserFinancialAccountColumns::ID_USER, UserFinancialAccountColumns::ID_FINANCIAL_ACCOUNT], 'user_financial_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table);
        Schema::enableForeignKeyConstraints();
    }
};