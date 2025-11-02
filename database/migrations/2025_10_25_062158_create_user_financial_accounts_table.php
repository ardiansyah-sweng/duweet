<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\UserColumns;
use App\Constants\FinancialAccountColumns;

return new class extends Migration
{
    protected string $table;
    protected string $userTable;
    protected string $financialAccountTable;

    public function __construct()
    {
        $this->table = config('db_tables.user_financial_account');
        $this->userTable = config('db_tables.user');
        $this->financialAccountTable = config('db_tables.financial_account');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserFinancialAccountColumns::ID);

            // Foreign keys
            $table->foreignId(UserFinancialAccountColumns::USER_ID)
                ->constrained($this->userTable)
                ->onDelete('cascade'); // jika user dihapus, data ini ikut hilang

            $table->foreignId(UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained($this->financialAccountTable)
                ->onDelete('restrict'); // akun keuangan tidak boleh dihapus jika masih dipakai user

            // Balance columns
            $table->bigInteger(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->bigInteger(UserFinancialAccountColumns::BALANCE)->default(0);

            // Status & audit
            $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index([
                UserFinancialAccountColumns::USER_ID,
                UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID,
            ], 'idx_user_financial_account');

            $table->index(UserFinancialAccountColumns::IS_ACTIVE);
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
