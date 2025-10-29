<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;
    protected string $userTable;
    protected string $accountTable;

    public function __construct()
    {
        $this->table        = config('db_tables.user_account', 'user_financial_accounts');
        $this->userTable    = config('db_tables.user', 'users');
        $this->accountTable = config('db_tables.account', 'financial_accounts');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained($this->userTable)
                ->onDelete('cascade');

            $table->foreignId('financial_account_id')
                ->constrained($this->accountTable)
                ->onDelete('restrict');

            $table->bigInteger('initial_balance')->default(0);
            $table->bigInteger('balance')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id','financial_account_id']);
            $table->index(['user_id','is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
