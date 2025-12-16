<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserFinancialAccountColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.user_financial_account', 'user_financial_account');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserFinancialAccountColumns::ID);

            $table->foreignId(UserFinancialAccountColumns::USER_ACCOUNT_ID)
                ->constrained('user_accounts')
                ->onDelete('cascade');

            $table->foreignId(UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained('financial_accounts')
                ->onDelete('restrict');

            $table->bigInteger(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->bigInteger(UserFinancialAccountColumns::BALANCE)->default(0);
            $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
