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
        Schema::create('user_financial_accounts', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('financial_account_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->integer('balance')->default(0);
    $table->integer('initial_balance')->default(0);
    $table->boolean('is_active')->default(true);

    $table->timestamps();
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