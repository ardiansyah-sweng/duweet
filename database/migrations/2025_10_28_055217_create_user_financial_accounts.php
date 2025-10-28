<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Constants\UserFinancialAccountColumns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_financial_accounts', function (Blueprint $table) {
            $table->id(UserFinancialAccountColumns::ID);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->onDelete('cascade');
            $table->bigInteger(UserFinancialAccountColumns::BALANCE);
            $table->bigInteger(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);

            $table->timestamps();

            $table->unique([
                UserFinancialAccountColumns::USER_ID,
                UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_financial_accounts');
        Schema::enableForeignKeyConstraints();
    }
};
