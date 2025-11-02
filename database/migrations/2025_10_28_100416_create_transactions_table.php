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

            $table->uuid('transaction_group_id')->nullable();

            $table->foreignId('user_id')
                ->constrained($this->userTable)
                ->cascadeOnDelete();

            $table->foreignId('account_id')
                ->references('id')
                ->on('financial_accounts')
                ->onDelete('restrict');


            $table->enum('entry_type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->string('description', 255)->nullable();
            $table->boolean('is_balance')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['account_id', 'created_at']);
            $table->index(['transaction_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
