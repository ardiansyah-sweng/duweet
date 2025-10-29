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

            $table->string('transaction_group_id', 36);

            $table->foreignId('user_id')
                ->constrained($this->userTable)
                ->onDelete('cascade');

            $table->foreignId('account_id')
                ->constrained($this->financialAccountTable)
                ->onDelete('restrict');

            $table->enum('entry_type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);

            $table->string('description');
            $table->boolean('is_balance')->default(false);

            $table->timestamps();

            $table->index('user_id');
            $table->index('account_id');
            $table->index('transaction_group_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
