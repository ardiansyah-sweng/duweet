<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table = 'transaksi';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date');
            $table->string('description')->nullable();
            $table->bigInteger('amount');
            $table->enum('type', ['debit', 'credit'])->default('debit');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            // If you have users table, keep this FK; else remove
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['account_id', 'date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
