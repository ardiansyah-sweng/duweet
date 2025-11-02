<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table = 'transactions';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->uuid('transaction_group_id');

            // Foreign keys
            $table->foreignId('user_account_id')
                ->constrained('user_accounts')
                ->onDelete('cascade');

            $table->foreignId('financial_account_id')
                ->constrained('financial_accounts')
                ->onDelete('cascade');

            $table->enum('entry_type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount');
            $table->enum('balance_effect', ['increase', 'decrease']);
            $table->string('description');
            $table->boolean('is_balance')->default(false);
            $table->timestamps();

            $table->index('transaction_group_id');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table);
        Schema::enableForeignKeyConstraints();
    }
};
