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
        $this->userTable = config('db_tables.user_account');
        $this->financialAccountTable = config('db_tables.financial_account');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserFinancialAccountColumns::ID);

            $table->foreignId(UserFinancialAccountColumns::USER_ACCOUNT_ID)
                ->constrained($this->userTable)
                ->onDelete('cascade');

            $table->foreignId(UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained($this->financialAccountTable)
                ->onDelete('restrict');

            $table->bigInteger(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->bigInteger(UserFinancialAccountColumns::BALANCE)->default(0);

            $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);
            $table->timestamps();

            $table->index([
                UserFinancialAccountColumns::USER_ACCOUNT_ID,
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